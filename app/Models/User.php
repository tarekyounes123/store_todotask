<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Notifications\CustomPasswordResetNotification;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    public function tasks()
{
    return $this->hasMany(Task::class);
}

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'email_verified_at',
        'last_seen',
        'role',
        'phone_number',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen' => 'datetime',
        ];
    }

    /**
     * Get the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // When sending the notification, make sure we also create a record in our custom table
        \App\Models\PasswordResetToken::createNewToken(
            $this->getEmailForPasswordReset(),
            hash('sha256', $token)  // Store hashed for security
        );

        $this->notify(new CustomPasswordResetNotification($token));
    }

    /**
     * The chats that belong to the user.
     */
    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_participants');
    }

    /**
     * The messages sent by the user.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * The products that the user has favorited.
     */
    public function favoriteProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'user_product_favorites', 'user_id', 'product_id')->withTimestamps();
    }

    /**
     * Get the cart for the user.
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
