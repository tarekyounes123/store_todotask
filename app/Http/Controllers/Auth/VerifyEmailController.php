<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function verify($id, $hash): RedirectResponse
    {
        // Find the user by ID
        $user = User::findOrFail($id);

        // Check if the currently authenticated user is the same as the verification user
        $isCurrentUser = Auth::check() && Auth::id() === $user->id;

        // Check if the user's email is already verified
        if ($user->hasVerifiedEmail()) {
            // If already verified, redirect to dashboard
            if (!$isCurrentUser) {
                // If not the current user, log them in
                if (Auth::check()) {
                    Auth::logout();
                }
                Auth::login($user);
            }
            return redirect()->route('dashboard')
                             ->with('status', 'Your email is already verified.');
        }

        // Mark the email as verified
        $user->markEmailAsVerified();
        event(new Verified($user));

        // If the current user is not the verification user, log them out and log in the verification user
        if (!$isCurrentUser) {
            if (Auth::check()) {
                Auth::logout();
            }
            Auth::login($user);
        }

        return redirect()->route('dashboard')
                         ->with('verified', true)
                         ->with('status', 'Your email has been verified successfully!');
    }
}
