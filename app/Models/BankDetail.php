<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankDetail extends Model
{
    protected $fillable = [
        'farmer_id',
        'bank_name',
        'account_name',
        'account_number',
        'account_type',
        'routing_number',
        'swift_code',
        'branch_address',
        'instructions',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function getFormattedAccountNumberAttribute(): string
    {
        // Mask account number for security (show last 4 digits)
        return '****' . substr($this->account_number, -4);
    }

    public function getFullAccountDetailsAttribute(): string
    {
        return "{$this->bank_name} - {$this->account_name} ({$this->formatted_account_number})";
    }
}
