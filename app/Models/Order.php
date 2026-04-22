<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentReceipt;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'order_number',
        'total_amount',
        'status',
        'delivery_address',
        'phone',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the buyer that owns the order
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the order items for the order
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the crops associated with this order
     */
    public function crops()
    {
        return $this->belongsToMany(Crop::class, 'order_items')
                    ->withPivot(['quantity', 'price_per_kg', 'total_price']);
    }

    /**
     * Scope a query to only include pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed orders
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include delivered orders
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Generate a unique order number
     */
    public static function generateOrderNumber()
    {
        $latestOrder = self::latest()->first();
        $orderNumber = 'ORD' . date('Ymd') . '001';
        
        if ($latestOrder) {
            $lastOrderNumber = $latestOrder->order_number;
            $lastSequence = intval(substr($lastOrderNumber, -3));
            $orderNumber = 'ORD' . date('Ymd') . str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
        }
        
        return $orderNumber;
    }

    /**
     * Get the formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return 'TZS' . number_format($this->total_amount, 2);
    }

    /**
     * Get payment receipts for this order
     */
    public function paymentReceipts()
    {
        return $this->hasMany(PaymentReceipt::class, 'order_id');
    }

    /**
     * Get the status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'pending_payment' => '<span class="badge bg-secondary">Pending Payment</span>',
            'payment_rejected' => '<span class="badge bg-danger">Payment Rejected</span>',
            'confirmed' => '<span class="badge bg-info">Confirmed</span>',
            'delivered' => '<span class="badge bg-success">Delivered</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
        ];
        
        return $badges[$this->status] ?? $badges['pending'];
    }
}
