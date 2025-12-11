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
        Schema::create('warmings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sending_identity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_list_id')->constrained()->cascadeOnDelete();
            $table->string('plan'); // slow, standard, fast
            $table->string('status')->default('inactive'); // inactive, running, paused, finished
            $table->unsignedInteger('day_current')->default(1);
            $table->unsignedInteger('day_total')->default(30);
            $table->unsignedInteger('daily_target')->default(0);
            $table->json('schedule')->nullable();
            $table->string('subject')->default('Test MailMoon');
            $table->text('body')->nullable();
            $table->unsignedInteger('send_interval_seconds')->default(30);
            $table->unsignedInteger('sent_today')->default(0);
            $table->unsignedInteger('total_sent')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warmings');
    }
};
