<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login_at',
        'role',
        'phone',
        'address',
        'region',
        'avatar',
        'email_verified_at',
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
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a farmer
     */
    public function isFarmer(): bool
    {
        return $this->role === 'farmer';
    }

    /**
     * Check if user is a buyer
     */
    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    /**
     * Check if user is verified
     */
    public function isVerified(): bool
    {
        if (!$this->verification) {
            return false;
        }
        
        return $this->verification->isFullyVerified();
    }

    /**
     * Get crops for the farmer
     */
    public function crops()
    {
        return $this->hasMany(Crop::class, 'farmer_id');
    }

    /**
     * Get orders for the buyer
     */
    public function buyerOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    /**
     * Get order items for the farmer
     */
    public function farmerOrderItems()
    {
        return $this->hasMany(OrderItem::class, 'farmer_id');
    }

    /**
     * Get chats for the user
     */
    public function chats()
    {
        return Chat::where(function($query) {
            $query->where('buyer_id', $this->id)
                  ->orWhere('farmer_id', $this->id);
        })->get();
    }

    /**
     * Get bank details for the farmer
     */
    public function bankDetails()
    {
        return $this->hasOne(BankDetail::class, 'farmer_id');
    }

    /**
     * Get user verification details
     */
    public function verification()
    {
        return $this->hasOne(UserVerification::class);
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && $this->avatar !== 'default-avatar.png') {
            return asset('avatars/' . $this->avatar);
        }
        return asset('images/default-avatar.png');
    }

    /**
     * Send email verification notification
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\EmailVerificationNotification());
    }

    /**
     * Get email verification URL
     */
    public function getEmailVerificationUrl()
    {
        return route('verification.verify', [
            'id' => $this->getKey(),
            'hash' => sha1($this->getEmailForVerification()),
        ]);
    }
}
