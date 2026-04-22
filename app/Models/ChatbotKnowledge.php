<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotKnowledge extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'question',
        'answer',
        'keywords',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get active knowledge entries
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to search by keywords
     */
    public function scopeSearchByKeywords($query, $keywords)
    {
        return $query->where(function ($q) use ($keywords) {
            $q->where('question', 'like', "%{$keywords}%")
              ->orWhere('answer', 'like', "%{$keywords}%")
              ->orWhere('keywords', 'like', "%{$keywords}%");
        });
    }

    /**
     * Scope to get by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
