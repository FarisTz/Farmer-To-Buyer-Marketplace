<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'content',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the chat this message belongs to
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Get the sender of this message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get formatted timestamp
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('h:i A');
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('M d, Y');
    }

    /**
     * Check if message is from current user
     */
    public function isFromCurrentUser(): bool
    {
        return $this->sender_id === auth()->id();
    }

    /**
     * Get message alignment class
     */
    public function getAlignmentClass(): string
    {
        return $this->isFromCurrentUser() ? 'justify-content-end' : 'justify-content-start';
    }

    /**
     * Get message background class
     */
    public function getBackgroundClass(): string
    {
        return $this->isFromCurrentUser() ? 'bg-primary text-white' : 'bg-light';
    }

    /**
     * Get message text color class
     */
    public function getTextClass(): string
    {
        return $this->isFromCurrentUser() ? 'text-white' : 'text-dark';
    }

    /**
     * Get sender name
     */
    public function getSenderName(): string
    {
        return $this->sender->name;
    }

    /**
     * Get sender role badge
     */
    public function getSenderRoleBadge(): string
    {
        $role = $this->sender->role;
        $color = $role === 'farmer' ? 'success' : ($role === 'buyer' ? 'info' : 'danger');
        return "<span class='badge bg-{$color}'>{$role}</span>";
    }
}
