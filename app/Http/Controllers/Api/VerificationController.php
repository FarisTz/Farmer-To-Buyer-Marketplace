<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    /**
     * Get user verification status
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $verification = $user->verification;

        return response()->json([
            'success' => true,
            'data' => [
                'verification' => $verification,
                'is_verified' => $user->isVerified(),
                'verification_status' => $verification ? $verification->status : null,
            ]
        ]);
    }

    /**
     * Submit ID verification
     */
    public function storeId(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'id_type' => 'required|in:national_id,passport,driving_license',
            'id_number' => 'required|string|max:50',
            'id_front_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'id_back_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'selfie_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

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

        $verification->update([
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'id_status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ID verification submitted successfully',
            'data' => [
                'verification' => $verification->fresh()
            ]
        ]);
    }

    /**
     * Send phone verification code
     */
    public function sendPhone(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $verification = $user->verification ?? new UserVerification(['user_id' => $user->id]);

        // Generate 6-digit code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $verification->update([
            'phone_number' => $request->phone_number,
            'phone_verification_code' => $verificationCode,
            'phone_code_expires_at' => now()->addMinutes(10),
            'phone_status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent to your phone',
            'data' => [
                'phone_number' => $request->phone_number,
                'code_expires_at' => $verification->phone_code_expires_at,
            ]
        ]);
    }

    /**
     * Verify phone code
     */
    public function verifyPhone(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'phone_verification_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $verification = $user->verification;
        
        if (!$verification || $verification->phone_verification_code !== $request->phone_verification_code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code'
            ], 400);
        }

        if (now()->gt($verification->phone_code_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired'
            ], 400);
        }

        $verification->update([
            'phone_verification_code' => null,
            'phone_code_expires_at' => null,
            'phone_status' => 'verified',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Phone number verified successfully',
            'data' => [
                'verification' => $verification->fresh()
            ]
        ]);
    }

    /**
     * Submit address verification
     */
    public function storeAddress(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'verification_document' => 'required|in:utility_bill,lease_agreement,bank_statement',
            'address_proof_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $verification = $user->verification ?? new UserVerification(['user_id' => $user->id]);

        if ($request->hasFile('address_proof_image')) {
            $path = $request->file('address_proof_image')->store('verifications/address', 'public');
            $verification->address_proof_image = $path;
        }

        $verification->update([
            'verification_document' => $request->verification_document,
            'address_status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address verification submitted successfully',
            'data' => [
                'verification' => $verification->fresh()
            ]
        ]);
    }
}
