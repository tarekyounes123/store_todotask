<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google authentication.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();

            // Check if the user already exists in our database
            $existingUser = User::where('email', $user->email)->first();

            if ($existingUser) {
                // If user exists, log them in
                Auth::login($existingUser, true);
            } else {
                // If user doesn't exist, create a new user
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => now(), // Google verified emails are trusted
                    'password' => bcrypt('password'), // Generate a random password
                    'profile_picture' => $user->avatar,
                ]);

                Auth::login($newUser, true);
            }

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Google authentication error: ' . $e->getMessage());
            
            return redirect('/login')->withErrors([
                'google' => 'Google authentication failed. Please try again.'
            ]);
        }
    }
}