<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->json('extra_subjects')->nullable()->after('subject');
            $table->json('extra_contents')->nullable()->after('html_content');
            $table->unsignedInteger('send_interval_max_seconds')->nullable()->after('send_interval_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['extra_subjects', 'extra_contents', 'send_interval_max_seconds']);
        });
    }
};
