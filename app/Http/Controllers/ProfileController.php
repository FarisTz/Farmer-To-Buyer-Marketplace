<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\ActivityLogger;
use App\Notifications\EmailVerificationNotification;

class ProfileController extends Controller
{
    /**
     * Show user profile
     */
    public function show(): View
    {
        return view('profile.show', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $originalData = $user->getOriginal();
        
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Send email verification if email was changed
        if ($user->wasChanged('email')) {
            $user->sendEmailVerificationNotification();
            $emailChangedMessage = 'Profile updated! Please check your new email address for verification.';
        } else {
            $emailChangedMessage = 'Profile updated successfully!';
        }

        // Log profile update activity
        $changes = [];
        foreach ($user->getDirty() as $key => $value) {
            $changes[$key] = [
                'old' => $originalData[$key] ?? null,
                'new' => $value
            ];
        }
        
        if (!empty($changes)) {
            ActivityLogger::profileUpdated($user, $changes);
        }

        return Redirect::route('profile.show')->with('success', $emailChangedMessage);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('profile.show')
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Update user avatar
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar && $user->avatar !== 'default-avatar.png') {
            Storage::delete('avatars/' . $user->avatar);
        }

        // Store new avatar
        $avatarName = time() . '.' . $validated['avatar']->getClientOriginalExtension();
        $validated['avatar']->storeAs('avatars', $avatarName, 'public');

        $user->update(['avatar' => $avatarName]);

        return Redirect::route('profile.show')
            ->with('success', 'Avatar updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
