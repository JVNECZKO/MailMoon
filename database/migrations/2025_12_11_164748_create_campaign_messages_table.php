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
        Schema::create('campaign_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->string('to_email');
            $table->string('message_id')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('open_count')->default(0);
            $table->timestamp('first_open_at')->nullable();
            $table->unsignedInteger('click_count')->default(0);
            $table->timestamp('last_click_at')->nullable();
            $table->timestamp('unsubscribe_at')->nullable();
            $table->string('unsubscribe_token')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_messages');
    }
};
