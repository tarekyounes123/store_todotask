<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';

    // Disable timestamps since the table doesn't have updated_at column
    public $timestamps = false;

    // Set the primary key to email since that's the primary key in the table
    protected $primaryKey = 'email';

    // Disable auto-incrementing since email is not an auto-incrementing field
    public $incrementing = false;

    // Set key type to string since email is a string
    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'token',
        'used',
        'expires_at',
        'created_at',
    ];

    protected $casts = [
        'used' => 'boolean',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Check if the token has expired
     */
    public function isExpired(): bool
    {
        if ($this->expires_at) {
            return $this->expires_at->isPast();
        }

        // If no expires_at set, check if it's older than the configured time (60 minutes default)
        $expirationTime = config('auth.passwords.users.expire', 60);
        return $this->created_at->addMinutes($expirationTime)->isPast();
    }

    /**
     * Check if the token has been used
     */
    public function isUsed(): bool
    {
        return $this->used;
    }

    /**
     * Check if the token is valid (not used and not expired)
     */
    public function isValid(): bool
    {
        return !$this->isUsed() && !$this->isExpired();
    }

    /**
     * Mark the token as used
     */
    public function markAsUsed(): void
    {
        $this->fill([
            'used' => true,
        ])->save();
    }

    /**
     * Invalidate all previous tokens for the same email
     */
    public static function invalidatePreviousTokens(string $email): void
    {
        static::where('email', $email)->update(['used' => true]);
    }

    /**
     * Create a new token and invalidate previous tokens for the same email
     */
    public static function createNewToken(string $email, string $token): self
    {
        // Delete the previous token record for this email
        static::where('email', $email)->delete();

        // Create the new token
        return static::create([
            'email' => $email,
            'token' => $token,
            'used' => false,
            'expires_at' => now()->addMinutes(config('auth.passwords.users.expire', 60)),
            'created_at' => now(),
        ]);
    }
}