<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVerification extends Model
{
    protected $fillable = [
        'user_id',
        'id_type',
        'id_number',
        'id_front_image',
        'id_back_image',
        'selfie_image',
        'phone_number',
        'phone_verification_code',
        'phone_verified_at',
        'verification_document',
        'address_proof_image',
        'status',
        'rejection_reason',
        'admin_notes',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'phone_verified_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the verification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who reviewed the verification.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if ID is verified
     */
    public function isIdVerified(): bool
    {
        return $this->status === 'verified' && 
               !empty($this->id_type) && 
               !empty($this->id_number) && 
               !empty($this->id_front_image);
    }

    /**
     * Check if phone is verified
     */
    public function isPhoneVerified(): bool
    {
        return !empty($this->phone_verified_at);
    }

    /**
     * Check if address is verified
     */
    public function isAddressVerified(): bool
    {
        return $this->status === 'verified' && 
               !empty($this->verification_document) && 
               !empty($this->address_proof_image);
    }

    /**
     * Check if user is fully verified (ID, phone, and address)
     */
    public function isFullyVerified(): bool
    {
        return $this->isIdVerified() && 
               $this->isPhoneVerified() && 
               $this->isAddressVerified();
    }

    /**
     * Get verification completion percentage
     */
    public function getCompletionPercentage(): int
    {
        $totalFields = 7; // id_type, id_number, id_front, id_back, selfie, phone, address
        $completedFields = 0;

        if (!empty($this->id_type)) $completedFields++;
        if (!empty($this->id_number)) $completedFields++;
        if (!empty($this->id_front_image)) $completedFields++;
        if (!empty($this->id_back_image)) $completedFields++;
        if (!empty($this->selfie_image)) $completedFields++;
        if ($this->isPhoneVerified()) $completedFields++;
        if (!empty($this->address_proof_image)) $completedFields++;

        return (int) (($completedFields / $totalFields) * 100);
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadge(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'under_review' => '<span class="badge bg-info">Under Review</span>',
            'verified' => '<span class="badge bg-success">Verified</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    /**
     * Scope for pending verifications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for verified users
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope for rejected verifications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
