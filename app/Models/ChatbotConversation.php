<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatbotConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'user_message',
        'bot_response',
        'knowledge_id',
        'intent',
        'confidence_score',
        'user_rating',
        'was_helpful',
        'user_feedback',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'was_helpful' => 'boolean',
        'confidence_score' => 'integer',
        'user_rating' => 'integer',
    ];

    /**
     * Relationship to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to knowledge base
     */
    public function knowledge()
    {
        return $this->belongsTo(ChatbotKnowledge::class);
    }

    /**
     * Scope for helpful conversations
     */
    public function scopeHelpful($query)
    {
        return $query->where('was_helpful', true);
    }

    /**
     * Scope for unhelpful conversations
     */
    public function scopeUnhelpful($query)
    {
        return $query->where('was_helpful', false);
    }

    /**
     * Scope by intent
     */
    public function scopeByIntent($query, $intent)
    {
        return $query->where('intent', $intent);
    }

    /**
     * Scope for recent conversations
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get average rating
     */
    public static function getAverageRating()
    {
        return static::whereNotNull('user_rating')->avg('user_rating');
    }

    /**
     * Get success rate
     */
    public static function getSuccessRate()
    {
        $total = static::whereNotNull('was_helpful')->count();
        $helpful = static::where('was_helpful', true)->count();
        
        return $total > 0 ? ($helpful / $total) * 100 : 0;
    }
}
