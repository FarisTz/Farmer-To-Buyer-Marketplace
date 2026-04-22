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
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable(); // For anonymous users
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->text('user_message');
            $table->text('bot_response');
            $table->foreignId('knowledge_id')->nullable()->constrained('chatbot_knowledge')->onDelete('set null');
            $table->string('intent')->nullable(); // Detected intent
            $table->integer('confidence_score')->default(0); // How confident the bot was
            $table->integer('user_rating')->nullable(); // User feedback 1-5
            $table->boolean('was_helpful')->nullable(); // User feedback
            $table->text('user_feedback')->nullable(); // Additional feedback
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_conversations');
    }
};
