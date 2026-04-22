<?php

namespace App\Http\Controllers;

use App\Models\UserVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VerificationController extends Controller
{
    /**
     * Show verification page
     */
    public function index()
    {
        $user = Auth::user();
        $verification = $user->verification ?? new UserVerification(['user_id' => $user->id]);
        
        return view('verification.index', compact('verification'));
    }

    /**
     * Store ID verification
     */
    public function storeIdVerification(Request $request)
    {
        $validated = $request->validate([
            'id_type' => 'required|string|in:national_id,passport,driving_license',
            'id_number' => 'required|string|max:50',
            'id_front_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'id_back_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'selfie_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();
        $verification = $user->verification ?? new UserVerification(['user_id' => $user->id]);

        // Handle file uploads
        if ($request->hasFile('id_front_image')) {
            $path = $request->file('id_front_image')->store('verifications/id_front', 'public');
            $verification->id_front_image = $path;
        }

        if ($request->hasFile('id_back_image')) {
            $path = $request->file('id_back_image')->store('verifications/id_back', 'public');
            $verification->id_back_image = $path;
        }

        if ($request->hasFile('selfie_image')) {
            $path = $request->file('selfie_image')->store('verifications/selfie', 'public');
            $verification->selfie_image = $path;
        }

        $verification->id_type = $validated['id_type'];
        $verification->id_number = $validated['id_number'];
        
        if ($verification->status === null) {
            $verification->status = 'pending';
            $verification->submitted_at = now();
        }

        $verification->save();

        return back()->with('success', 'ID verification submitted successfully!');
    }

    /**
     * Send phone verification code
     */
    public function sendPhoneVerification(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'required|string|regex:/^[\+]?[0-9]{10,15}$/',
        ], [
            'phone_number.regex' => 'Please enter a valid phone number (10-15 digits, may start with +)',
        ]);

        $user = Auth::user();
        $verification = $user->verification ?? new UserVerification(['user_id' => $user->id]);

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $verification->phone_number = $validated['phone_number'];
        $verification->phone_verification_code = $code;
        $verification->save();

        // In production, send SMS via service provider
        // For now, store in session for demo
        session(['phone_verification_code' => $code]);

        return back()->with('success', 'Verification code sent to your phone!');
    }

    /**
     * Verify phone code
     */
    public function verifyPhone(Request $request)
    {
        $validated = $request->validate([
            'phone_verification_code' => 'required|string|size:6',
        ], [
            'phone_verification_code.required' => 'Please enter the verification code',
            'phone_verification_code.size' => 'Verification code must be exactly 6 digits',
        ]);

        $user = Auth::user();
        $verification = $user->verification;

        if (!$verification || $verification->phone_verification_code !== $validated['phone_verification_code']) {
            return back()->with('error', 'Invalid verification code. Please check and try again.');
        }

        $verification->phone_verified_at = now();
        $verification->phone_verification_code = null;
        $verification->save();

        return back()->with('success', 'Phone number verified successfully!');
    }

    /**
     * Store address verification
     */
    public function storeAddressVerification(Request $request)
    {
        $validated = $request->validate([
            'verification_document' => 'required|string|in:utility_bill,lease_agreement,bank_statement',
            'address_proof_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'verification_document.required' => 'Please select a document type',
            'verification_document.in' => 'Please select a valid document type',
            'address_proof_image.required' => 'Please upload your address proof document',
            'address_proof_image.image' => 'Please upload a valid image file',
            'address_proof_image.mimes' => 'Only JPEG, PNG, and JPG files are allowed',
            'address_proof_image.max' => 'File size must be less than 5MB',
        ]);

        $user = Auth::user();
        $verification = $user->verification ?? new UserVerification(['user_id' => $user->id]);

        if ($request->hasFile('address_proof_image')) {
            $path = $request->file('address_proof_image')->store('verifications/address', 'public');
            $verification->address_proof_image = $path;
        }

        $verification->verification_document = $validated['verification_document'];
        
        if ($verification->status === null) {
            $verification->status = 'pending';
            $verification->submitted_at = now();
        }

        $verification->save();

        return back()->with('success', 'Address verification submitted successfully!');
    }

    /**
     * Submit all verifications for review
     */
    public function submitForReview(Request $request)
    {
        $user = Auth::user();
        $verification = $user->verification;

        if (!$verification) {
            return back()->with('error', 'Please complete at least one verification before submitting!');
        }

        $verification->status = 'under_review';
        $verification->submitted_at = now();
        $verification->save();

        return back()->with('success', 'Your verification has been submitted for review!');
    }

    /**
     * Admin: List pending verifications
     */
    public function adminIndex()
    {
        $verifications = UserVerification::with(['user', 'reviewer'])
            ->whereIn('status', ['pending', 'under_review'])
            ->latest('submitted_at')
            ->paginate(10);

        return view('admin.verifications.index', compact('verifications'));
    }

    /**
     * Admin: Show verification details
     */
    public function adminShow(UserVerification $verification)
    {
        $verification->load(['user', 'reviewer']);
        return view('admin.verifications.show', compact('verification'));
    }

    /**
     * Admin: Approve verification
     */
    public function adminApprove(Request $request, UserVerification $verification)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $verification->status = 'verified';
        $verification->reviewed_at = now();
        $verification->reviewed_by = Auth::id();
        $verification->admin_notes = $request->admin_notes;
        $verification->save();

        return redirect()->route('admin.verifications.index')
            ->with('success', 'Verification approved successfully!');
    }

    /**
     * Admin: Reject verification
     */
    public function adminReject(Request $request, UserVerification $verification)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $verification->status = 'rejected';
        $verification->rejection_reason = $request->rejection_reason;
        $verification->admin_notes = $request->admin_notes;
        $verification->reviewed_at = now();
        $verification->reviewed_by = Auth::id();
        $verification->save();

        return redirect()->route('admin.verifications.index')
            ->with('success', 'Verification rejected successfully!');
    }

    /**
     * Get verification status for API
     */
    public function getStatus()
    {
        $user = Auth::user();
        $verification = $user->verification;

        return response()->json([
            'status' => $verification->status ?? 'not_started',
            'completion_percentage' => $verification->getCompletionPercentage() ?? 0,
            'is_id_verified' => $verification->isIdVerified() ?? false,
            'is_phone_verified' => $verification->isPhoneVerified() ?? false,
            'is_address_verified' => $verification->isAddressVerified() ?? false,
        ]);
    }
}
