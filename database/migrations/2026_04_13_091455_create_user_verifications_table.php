<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // ID Verification
            $table->string('id_type')->nullable(); // national_id, passport, drivers_license
            $table->string('id_number')->nullable();
            $table->string('id_front_image')->nullable();
            $table->string('id_back_image')->nullable();
            $table->string('selfie_image')->nullable(); // User holding ID
            
            // Phone Verification
            $table->string('phone_number')->nullable();
            $table->string('phone_verification_code')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            
            // Address Verification
            $table->string('verification_document')->nullable(); // Utility bill, lease agreement
            $table->string('address_proof_image')->nullable();
            
            // Verification Status
            $table->enum('status', ['pending', 'under_review', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Verification Metadata
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['status', 'submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_verifications');
    }
};
