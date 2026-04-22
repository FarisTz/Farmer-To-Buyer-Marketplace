<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceipt extends Model
{
    protected $fillable = [
        'order_id',
        'buyer_id',
        'farmer_id',
        'receipt_image',
        'amount_paid',
        'payment_method',
        'transaction_reference',
        'notes',
        'status',
        'payment_date',
        'verified_at',
        'verified_by',
        'rejection_reason'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'verified' => '<span class="badge bg-success">Verified</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-secondary">' . $this->status . '</span>'
        };
    }

    public function getReceiptImageUrlAttribute(): string
    {
        return asset('storage/receipts/' . $this->receipt_image);
    }
}
