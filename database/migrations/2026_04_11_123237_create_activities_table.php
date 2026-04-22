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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // login, logout, crop_created, crop_updated, order_created, order_updated, etc.
            $table->text('description');
            $table->string('subject_type')->nullable(); // User, Crop, Order, etc.
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('causer_type')->nullable(); // User who performed the action
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->json('properties')->nullable(); // Old values, new values, etc.
            $table->ipAddress()->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['subject_type', 'subject_id']);
            $table->index(['causer_type', 'causer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
