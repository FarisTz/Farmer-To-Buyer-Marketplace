<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'crop_id',
        'farmer_id',
        'quantity',
        'price_per_kg',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the order that owns the order item
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the crop that owns the order item
     */
    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }

    /**
     * Get the farmer that owns the order item
     */
    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    /**
     * Get the formatted total price
     */
    public function getFormattedTotalPriceAttribute()
    {
        return 'TZS' . number_format($this->total_price, 2);
    }

    /**
     * Get the formatted price per kg
     */
    public function getFormattedPricePerKgAttribute()
    {
        return 'TZS' . number_format($this->price_per_kg, 2);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Calculate total price when quantity or price changes
        static::saving(function ($orderItem) {
            $orderItem->total_price = $orderItem->quantity * $orderItem->price_per_kg;
        });
    }
}
