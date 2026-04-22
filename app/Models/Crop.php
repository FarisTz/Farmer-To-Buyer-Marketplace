<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'name',
        'description',
        'price_per_kg',
        'available_quantity',
        'unit',
        'category',
        'region',
        'image',
        'is_available',
    ];

    protected $casts = [
        'price_per_kg' => 'decimal:2',
        'available_quantity' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Get the farmer that owns the crop
     */
    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    /**
     * Get the order items for this crop
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope a query to only include available crops
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope a query to filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to filter by region
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Scope a query to filter by price range
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price_per_kg', [$minPrice, $maxPrice]);
    }

    /**
     * Get the formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'TZS' . number_format($this->price_per_kg, 2);
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/crops/' . $this->image);
        }
        return asset('images/default-crop.jpg');
    }

    /**
     * Check if crop has active orders
     */
    public function hasActiveOrders()
    {
        return $this->orderItems()
            ->whereHas('order', function($query) {
                $query->whereIn('status', ['pending', 'confirmed']);
            })
            ->exists();
    }
}
