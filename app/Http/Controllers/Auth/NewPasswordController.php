<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Find the user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        // Check if the token exists and is valid
        $tokenRecord = PasswordResetToken::where('email', $request->email)->first();

        if (!$tokenRecord) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'This password reset token is invalid.']);
        }

        // Check if the token has already been used
        if ($tokenRecord->isUsed()) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'This password reset token has already been used.']);
        }

        // Check if token is expired
        if ($tokenRecord->isExpired()) {
            // Clean up expired token
            $tokenRecord->delete();
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'This password reset token has expired.']);
        }

        // Verify that the token matches (comparing the hash)
        $hashedToken = hash('sha256', $request->token);
        if ($tokenRecord->token !== $hashedToken) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'This password reset token is invalid.']);
        }

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->remember_token = Str::random(60);
        $user->save();

        event(new PasswordReset($user));

        // Mark the token as used after successful password reset
        $tokenRecord->markAsUsed();

        return redirect()->route('login')->with('status', 'Your password has been reset!');
    }
}