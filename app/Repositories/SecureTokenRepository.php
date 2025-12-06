<?php

namespace App\Repositories;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use App\Models\PasswordResetToken;

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
        // Invalidate all previous tokens for this user in our custom table
        PasswordResetToken::invalidatePreviousTokens($user->getEmailForPasswordReset());
        
        $email = $user->getEmailForPasswordReset();
        
        // Create a new, random token
        $token = $this->createNewToken();

        // Create a record in our custom table with enhanced security
        PasswordResetToken::create([
            'email' => $email,
            'token' => hash('sha256', $token), // Store hashed for security
            'used' => false,
            'expires_at' => now()->addMinutes($this->expires),
            'created_at' => now(),
        ]);

        // Insert the token into the default table as Laravel expects
        $this->getTable()->insert([
            'email' => $email,
            'token' => $this->hasher->make($token),
            'created_at' => new Carbon,
        ]);

        return $token;
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

        // Check if the token matches using Laravel's hasher
        $valid = hash_equals($record['token'], $this->hasher->make($token)) ||
                 $this->hasher->check($token, $record['token']);

        if (! $valid) {
            return false;
        }

        // Check if token is expired based on creation time
        if ($this->tokenExpired($record['created_at'])) {
            $this->deleteEmail($user->getEmailForPasswordReset());
            return false;
        }

        // Verify the token exists in our custom table and hasn't been used
        $customToken = PasswordResetToken::where('email', $user->getEmailForPasswordReset())->first();
        
        if ($customToken) {
            // Verify token hash matches
            if (!hash_equals($customToken->token, hash('sha256', $token))) {
                return false;
            }
            
            // Check if it's already been used
            if ($customToken->isUsed()) {
                return false;
            }
            
            // Check if it's expired
            if ($customToken->isExpired()) {
                $customToken->delete();
                return false;
            }
        }

        return true;
    }

    /**
     * Delete all existing reset tokens from the database for the given email.
     *
     * @param  string  $email
     * @return void
     */
    public function deleteEmail($email)
    {
        $this->getTable()->where('email', $email)->delete();
        
        // Also delete from our custom table
        PasswordResetToken::where('email', $email)->delete();
    }
}