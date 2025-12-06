<?php

namespace App\Providers\Auth;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class SecureTokenRepository extends DatabaseTokenRepository
{
    /**
     * Create a new token record.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function create($user)
    {
        // Check if a token already exists for this user, invalidate it
        $this->invalidatePreviousTokens($user->getEmailForPasswordReset());
        
        // Create a new token
        $email = $user->getEmailForPasswordReset();
        $token = $this->createNewToken();
        
        $this->getTable()->insert([
            'email' => $email,
            'token' => hash('sha256', $token),
            'used' => false,
            'expires_at' => now()->addMinutes($this->expires),
            'created_at' => new Carbon,
        ]);

        return $token;
    }

    /**
     * Delete all existing reset tokens from the database for the given email.
     *
     * @param  string  $email
     * @return int
     */
    public function invalidatePreviousTokens($email)
    {
        return $this->getTable()->where('email', $email)->update(['used' => true]);
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function exists($user, $token)
    {
        $record = (array) $this->getTable()->where('email', $user->getEmailForPasswordReset())->first();

        if (! $record) {
            return false;
        }

        // Check if token exists, is not used, and not expired
        $tokenHash = hash('sha256', $token);
        
        if ($record['token'] !== $tokenHash) {
            return false;
        }
        
        if ((bool) $record['used']) {
            // Token has been used, delete it and return false
            $this->deleteExisting($user->getEmailForPasswordReset());
            return false;
        }
        
        if ($this->tokenExpired($record)) {
            // Token is expired, delete it and return false
            $this->deleteExisting($user->getEmailForPasswordReset());
            return false;
        }

        return true;
    }

    /**
     * Determine if the token has expired.
     *
     * @param  array  $token
     * @return bool
     */
    protected function tokenExpired($token)
    {
        if (!isset($token['expires_at'])) {
            // Fall back to the old expiration check
            return Carbon::parse($token['created_at'])->addSeconds($this->expires * 60)->isPast();
        }
        
        return Carbon::parse($token['expires_at'])->isPast();
    }

    /**
     * Delete all existing reset tokens from the database for the given email.
     *
     * @param  string  $email
     * @return void
     */
    protected function deleteExisting($email)
    {
        $this->getTable()->where('email', $email)->delete();
    }
}