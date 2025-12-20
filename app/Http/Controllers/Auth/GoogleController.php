<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        // Ensure session is properly maintained
        session()->regenerateToken();

        // Store the intended URL in the session to redirect back after login
        session()->put('url.intended', url()->previous());

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google authentication.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback(Request $request)
    {
        \Log::info('Google callback initiated');

        try {
            $googleUser = Socialite::driver('google')->user();

            \Log::info('Google user retrieved', [
                'id' => $googleUser->id,
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'avatar' => $googleUser->avatar
            ]);

            // Validate essential data from Google
            $validator = Validator::make([
                'email' => $googleUser->email,
                'name' => $googleUser->name,
            ], [
                'email' => 'required|email',
                'name' => 'required|string|min:1',
            ]);

            if ($validator->fails()) {
                \Log::warning('Google user validation failed', $validator->errors()->toArray());
                return redirect('/login')->withErrors([
                    'google' => 'Invalid profile data received from Google. Please update your Google profile.'
                ]);
            }

            // Check if user already exists with Google ID
            $existingGoogleUser = User::where('google_id', $googleUser->id)->first();
            \Log::info('Checked for existing Google user', ['found' => $existingGoogleUser ? true : false]);

            if ($existingGoogleUser) {
                // User already linked to Google - log them in
                \Log::info('Existing Google user found, logging in', ['user_id' => $existingGoogleUser->id]);
                if ($this->isAccountActive($existingGoogleUser)) {
                    Auth::login($existingGoogleUser, true);
                    return redirect()->route('dashboard');
                } else {
                    \Log::warning('Existing Google user is not active', ['user_id' => $existingGoogleUser->id]);
                    return redirect('/login')->withErrors([
                        'google' => 'Your account has been disabled. Please contact support.'
                    ]);
                }
            }

            // Check for existing user with same email
            $existingUser = User::where('email', $googleUser->email)->first();
            \Log::info('Checked for existing user by email', ['found' => $existingUser ? true : false]);

            if ($existingUser) {
                // Check if the existing user has a password (regular account)
                if ($existingUser->password) {
                    \Log::warning('Email already exists with password account', ['email' => $googleUser->email]);
                    // Account conflict - user exists with password authentication
                    return redirect('/login')->withErrors([
                        'email' => 'An account with this email already exists. Please log in with your password or use the "Forgot Password" option if needed.'
                    ]);
                } else {
                    // Existing user without password (possibly old OAuth account) - link Google account
                    \Log::info('Linking Google ID to existing user without password', ['user_id' => $existingUser->id]);
                    $existingUser->update([
                        'name' => $googleUser->name ?: $existingUser->name,
                        'google_id' => $googleUser->id,
                        'profile_picture' => $this->optimizeAvatarUrl($googleUser->avatar),
                        'email_verified_at' => now(),
                    ]);

                    if ($this->isAccountActive($existingUser)) {
                        Auth::login($existingUser, true);
                        return redirect()->route('dashboard');
                    } else {
                        \Log::warning('Existing user without password is not active', ['user_id' => $existingUser->id]);
                        return redirect('/login')->withErrors([
                            'google' => 'Your account has been disabled. Please contact support.'
                        ]);
                    }
                }
            }

            // Create new user
            \Log::info('Creating new user from Google', [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id
            ]);

            try {
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'email_verified_at' => now(), // Google emails are verified
                    'password' => Hash::make(Str::random(16)), // Secure random password
                    'google_id' => $googleUser->id,
                    'profile_picture' => $this->optimizeAvatarUrl($googleUser->avatar),
                    'status' => 'active', // Default to active
                ]);

                \Log::info('New user created successfully', ['user_id' => $newUser->id]);

                Auth::login($newUser, true);
                return redirect()->route('dashboard');
            } catch (\Exception $e) {
                \Log::error('Google authentication user creation error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'user_data' => [
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id
                    ]
                ]);

                return redirect('/login')->withErrors([
                    'google' => 'Unable to create account. Please try again or contact support.'
                ]);
            }

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            \Log::error('Google authentication client error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->withErrors([
                'google' => 'Google authentication service error. Please try again later.'
            ]);
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            \Log::error('Google authentication state error: ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'google' => 'Authentication state mismatch. Please try logging again.'
            ]);
        } catch (\Laravel\Socialite\Two\MissingArgumentException $e) {
            \Log::error('Google authentication missing argument error: ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'google' => 'Missing required authentication data. Please try again.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Google authentication error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->withErrors([
                'google' => 'Google authentication failed. Please try again.'
            ]);
        }
    }

    /**
     * Check if the user account is active
     */
    private function isAccountActive($user): bool
    {
        // Check if account is disabled
        if ($user->status === 'disabled' || $user->status === 'inactive') {
            return false;
        }

        // Check if account is suspended
        if ($user->suspended_until && $user->suspended_until->isFuture()) {
            return false;
        }

        return true;
    }

    /**
     * Optimize Google avatar URL to a reasonable size
     */
    private function optimizeAvatarUrl(?string $avatarUrl): ?string
    {
        if (!$avatarUrl) {
            return $avatarUrl;
        }

        // Optimize Google avatar size to 200x200
        if (strpos($avatarUrl, 'googleusercontent.com') !== false) {
            // Parse the URL to manipulate its components
            $urlParts = parse_url($avatarUrl);
            $queryParams = [];

            // Extract existing query parameters if any
            if (isset($urlParts['query'])) {
                parse_str($urlParts['query'], $queryParams);
            }

            // Remove any existing 'sz' parameter to prevent duplication
            unset($queryParams['sz']);

            // Add the desired size parameter
            $queryParams['sz'] = 200;

            // Reconstruct the query string
            $newQuery = http_build_query($queryParams);

            // Reconstruct the URL
            $optimizedUrl = $urlParts['scheme'] . '://' . $urlParts['host'] . (isset($urlParts['path']) ? $urlParts['path'] : '') . '?' . $newQuery;

            return $optimizedUrl;
        }

        return $avatarUrl;
    }
}