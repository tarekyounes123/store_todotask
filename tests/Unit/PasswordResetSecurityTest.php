<?php

namespace Tests\Unit;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordResetSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_previous_tokens_are_invalidated_when_new_one_is_requested(): void
    {
        $email = 'test@example.com';
        $user = User::factory()->create(['email' => $email]);

        // Create first token
        $firstToken = hash('sha256', Str::random(60));
        $firstTokenRecord = PasswordResetToken::create([
            'email' => $email,
            'token' => $firstToken,
            'used' => false,
            'expires_at' => now()->addHour(),
            'created_at' => now(),
        ]);

        // Verify first token exists and is valid
        $this->assertFalse($firstTokenRecord->isUsed());
        $this->assertFalse($firstTokenRecord->isExpired());
        $this->assertTrue($firstTokenRecord->isValid());

        // Create second token (this should delete the first one)
        $secondToken = hash('sha256', Str::random(60));
        $secondTokenRecord = PasswordResetToken::createNewToken($email, $secondToken);

        // Check that the second token exists and is not used
        $this->assertNotNull($secondTokenRecord);
        $this->assertFalse($secondTokenRecord->isUsed());
        $this->assertEquals($email, $secondTokenRecord->email);

        // Check that the first token no longer exists
        $updatedFirstToken = PasswordResetToken::where('email', $email)->where('token', $firstToken)->first();
        $this->assertNull($updatedFirstToken);

        // Check the current token in the table is the second one
        $currentToken = PasswordResetToken::where('email', $email)->first();
        $this->assertNotNull($currentToken);
        $this->assertEquals($secondToken, $currentToken->token);
        $this->assertFalse($currentToken->isUsed());
    }

    public function test_token_can_only_be_used_once(): void
    {
        $email = 'test@example.com';
        $user = User::factory()->create(['email' => $email]);

        $token = hash('sha256', Str::random(60));
        $passwordResetToken = PasswordResetToken::create([
            'email' => $email,
            'token' => $token,
            'used' => false,
            'expires_at' => now()->addHour(),
        ]);

        // Verify token is initially valid
        $this->assertFalse($passwordResetToken->isUsed());
        $this->assertFalse($passwordResetToken->isExpired());
        $this->assertTrue($passwordResetToken->isValid());

        // Mark the token as used
        $passwordResetToken->markAsUsed();

        // Verify token is now marked as used
        $passwordResetToken->refresh();
        $this->assertTrue($passwordResetToken->isUsed());
        $this->assertFalse($passwordResetToken->isValid());
    }

    public function test_expired_token_is_not_valid(): void
    {
        $email = 'test@example.com';
        $user = User::factory()->create(['email' => $email]);

        $token = hash('sha256', Str::random(60));
        $passwordResetToken = PasswordResetToken::create([
            'email' => $email,
            'token' => $token,
            'used' => false,
            'expires_at' => now()->subHour(), // Expired an hour ago
        ]);

        // Verify token is expired
        $this->assertFalse($passwordResetToken->isUsed());
        $this->assertTrue($passwordResetToken->isExpired());
        $this->assertFalse($passwordResetToken->isValid());
    }

    public function test_token_with_used_field_cannot_be_used_again(): void
    {
        $email = 'test@example.com';
        $user = User::factory()->create(['email' => $email]);

        $token = hash('sha256', Str::random(60));
        $passwordResetToken = PasswordResetToken::create([
            'email' => $email,
            'token' => $token,
            'used' => true, // Already used
            'expires_at' => now()->addHour(),
        ]);

        // Verify token is marked as used
        $this->assertTrue($passwordResetToken->isUsed());
        $this->assertFalse($passwordResetToken->isExpired());
        $this->assertFalse($passwordResetToken->isValid());
    }

    public function test_token_valid_method_works_correctly(): void
    {
        // Test valid token (not used and not expired)
        $validEmail = 'valid@example.com';
        $user = User::factory()->create(['email' => $validEmail]);
        $validToken = hash('sha256', Str::random(60));
        $validPasswordResetToken = PasswordResetToken::create([
            'email' => $validEmail,
            'token' => $validToken,
            'used' => false,
            'expires_at' => now()->addHour(),
        ]);
        $this->assertTrue($validPasswordResetToken->isValid());

        // Test used token
        $usedEmail = 'used@example.com';
        $user2 = User::factory()->create(['email' => $usedEmail]);
        $usedToken = hash('sha256', Str::random(60));
        $usedPasswordResetToken = PasswordResetToken::create([
            'email' => $usedEmail,
            'token' => $usedToken,
            'used' => true,
            'expires_at' => now()->addHour(),
        ]);
        $this->assertFalse($usedPasswordResetToken->isValid());

        // Test expired token
        $expiredEmail = 'expired@example.com';
        $user3 = User::factory()->create(['email' => $expiredEmail]);
        $expiredToken = hash('sha256', Str::random(60));
        $expiredPasswordResetToken = PasswordResetToken::create([
            'email' => $expiredEmail,
            'token' => $expiredToken,
            'used' => false,
            'expires_at' => now()->subHour(),
        ]);
        $this->assertFalse($expiredPasswordResetToken->isValid());

        // Test used and expired token
        $usedAndExpiredEmail = 'usedandexpired@example.com';
        $user4 = User::factory()->create(['email' => $usedAndExpiredEmail]);
        $usedAndExpiredToken = hash('sha256', Str::random(60));
        $usedAndExpiredPasswordResetToken = PasswordResetToken::create([
            'email' => $usedAndExpiredEmail,
            'token' => $usedAndExpiredToken,
            'used' => true,
            'expires_at' => now()->subHour(),
        ]);
        $this->assertFalse($usedAndExpiredPasswordResetToken->isValid());
    }
}