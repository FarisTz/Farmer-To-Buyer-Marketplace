<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get crops for this category
     */
    public function crops()
    {
        return $this->hasMany(Crop::class);
    }

    /**
     * Scope to order categories by name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
