<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->input('email'))->first();

        // Perform credential check to maintain timing consistency
        $validCredentials = $user &&
                           Hash::check($this->input('password'), $user->password ?? '') &&
                           $this->isAccountActive($user);

        if (!$validCredentials) {
            RateLimiter::hit($this->throttleKey());

            // Sleep to maintain consistent response time and prevent timing attacks
            usleep(random_int(100000, 300000));

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Successful authentication
        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);
    }

    /**
     * Check if the user account is active and can be logged into
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
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        // Sleep to maintain consistent timing
        usleep(random_int(100000, 300000));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
