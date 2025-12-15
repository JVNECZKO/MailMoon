<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->json('identity_rotation')->nullable()->after('sending_identity_id');
            $table->unsignedInteger('identity_rotation_index')->default(0)->after('identity_rotation');
        });

        Schema::create('campaign_sending_identity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sending_identity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['campaign_id', 'sending_identity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_sending_identity');
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['identity_rotation', 'identity_rotation_index']);
        });
    }
};
