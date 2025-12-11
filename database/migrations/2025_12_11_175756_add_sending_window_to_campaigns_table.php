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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->boolean('sending_window_enabled')->default(false)->after('send_interval_seconds');
            $table->time('sending_window_start')->nullable()->after('sending_window_enabled');
            $table->time('sending_window_end')->nullable()->after('sending_window_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['sending_window_enabled', 'sending_window_start', 'sending_window_end']);
        });
    }
};
