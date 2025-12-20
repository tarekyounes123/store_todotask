<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Use a database transaction to prevent race conditions
        $result = DB::transaction(function () use ($request) {
            $tokenRecord = PasswordResetToken::where('email', $request->email)->first();

            // If no token exists, return early but still simulate processing time to prevent timing attacks
            if (!$tokenRecord) {
                return ['success' => false, 'message' => 'If the information was valid, your password has been reset.'];
            }

            // Check if the token has already been used
            if ($tokenRecord->isUsed()) {
                return ['success' => false, 'message' => 'If the information was valid, your password has been reset.'];
            }

            // Check if token is expired
            if ($tokenRecord->isExpired()) {
                // Clean up expired token
                $tokenRecord->delete();
                return ['success' => false, 'message' => 'If the information was valid, your password has been reset.'];
            }

            // Verify that the token matches (comparing the hash)
            $hashedToken = hash('sha256', $request->token);
            if ($tokenRecord->token !== $hashedToken) {
                return ['success' => false, 'message' => 'If the information was valid, your password has been reset.'];
            }

            // Find and update the user's password
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return ['success' => false, 'message' => 'If the information was valid, your password has been reset.'];
            }

            // Update the user's password
            $user->password = Hash::make($request->password);
            $user->remember_token = Str::random(60);
            $user->save();

            event(new PasswordReset($user));

            // Mark the token as used after successful password reset
            $tokenRecord->markAsUsed();

            return ['success' => true, 'message' => 'Your password has been reset successfully!'];
        });

        // Simulate processing time to prevent timing attacks
        usleep(random_int(100000, 500000));

        if ($result['success']) {
            return redirect()->route('login')->with('status', $result['message']);
        } else {
            return redirect()->route('password.request')->withErrors(['email' => $result['message']]);
        }
    }
}