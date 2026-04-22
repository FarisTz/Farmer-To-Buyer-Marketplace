<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'farmer_id',
        'crop_id',
        'order_id',
        'subject',
        'last_message',
        'last_message_at',
        'is_active',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the buyer (user who started the chat)
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the farmer (user who receives messages)
     */
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    /**
     * Get the associated crop
     */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    /**
     * Get the associated order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get all messages in this chat
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get unread messages count for a specific user
     */
    public function unreadMessagesCount(?int $userId = null): int
    {
        $userId = $userId ?? auth()->id();
        
        return $this->messages()
            ->where('is_read', false)
            ->where('sender_id', '!=', $userId)
            ->count();
    }

    /**
     * Mark messages as read for a specific user
     */
    public function markAsRead(?int $userId = null): void
    {
        $userId = $userId ?? auth()->id();
        
        $this->messages()
            ->where('is_read', false)
            ->where('sender_id', '!=', $userId)
            ->update(['is_read' => true]);
    }

    /**
     * Get the other participant in the chat
     */
    public function getOtherParticipant(): User
    {
        $currentUserId = auth()->id();
        
        if ($this->buyer_id === $currentUserId) {
            return $this->farmer;
        }
        
        return $this->buyer;
    }

    /**
     * Get chat display name
     */
    public function getDisplayName(): string
    {
        $other = $this->getOtherParticipant();
        return $other->name;
    }

    /**
     * Get chat avatar
     */
    public function getAvatar(): string
    {
        $other = $this->getOtherParticipant();
        return $other->avatar ? asset('storage/avatars/' . $other->avatar) : '/images/default-avatar.png';
    }

    /**
     * Scope: Get chats for a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('buyer_id', $userId)
              ->orWhere('farmer_id', $userId);
        });
    }

    /**
     * Scope: Get active chats
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by last message
     */
    public function scopeOrderByLastMessage($query)
    {
        return $query->orderBy('last_message_at', 'desc');
    }
}
