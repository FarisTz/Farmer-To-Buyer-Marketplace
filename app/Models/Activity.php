<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphTo as MorphToRelation;

class Activity extends Model
{
    protected $fillable = [
        'type',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the user who caused the activity.
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the subject of the activity.
     */
    public function subject(): MorphToRelation
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include activities of a given type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include activities for a given user.
     */
    public function scopeForUser($query, $user)
    {
        return $query->where('causer_id', $user->id);
    }

    /**
     * Scope a query to only include activities within the last X days.
     */
    public function scopeLastDays($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get color for activity type.
     */
    public function typeColor(): string
    {
        return match($this->type) {
            'login' => 'success',
            'logout' => 'secondary',
            'user_registered' => 'primary',
            'profile_updated' => 'info',
            'crop_created' => 'success',
            'crop_updated' => 'warning',
            'crop_deleted' => 'danger',
            'order_created' => 'success',
            'order_status_updated' => 'warning',
            'admin_action' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get icon for activity type.
     */
    public function typeIcon(): string
    {
        return match($this->type) {
            'login' => 'sign-in-alt',
            'logout' => 'sign-out-alt',
            'user_registered' => 'user-plus',
            'profile_updated' => 'user-edit',
            'crop_created' => 'plus-circle',
            'crop_updated' => 'edit',
            'crop_deleted' => 'trash',
            'order_created' => 'shopping-cart',
            'order_status_updated' => 'exchange-alt',
            'admin_action' => 'cog',
            default => 'info-circle',
        };
    }
}
