<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Get the user to check if email exists
        $user = User::where('email', $request->email)->first();

        // For security, we send the same response whether the email exists or not
        // But for this implementation, we'll allow the reset to proceed if user exists
        if ($user) {
            // Invalidate all previous tokens for this email
            PasswordResetToken::invalidatePreviousTokens($request->email);

            // Send the reset link using Laravel's password broker
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return back()->with('status', 'Password reset link sent successfully! Please check your email.');
            }
        }

        // For security, we return the same message regardless of whether the email exists
        return back()->with('status', 'If your email address exists in our system, you will receive a password reset link shortly.');
    }
}