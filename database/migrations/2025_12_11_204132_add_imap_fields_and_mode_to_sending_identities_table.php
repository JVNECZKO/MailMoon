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
        Schema::table('sending_identities', function (Blueprint $table) {
            $table->string('send_mode')->default('smtp')->after('smtp_encryption'); // smtp | imap
            $table->string('imap_host')->nullable()->after('send_mode');
            $table->unsignedInteger('imap_port')->nullable()->after('imap_host');
            $table->string('imap_username')->nullable()->after('imap_port');
            $table->text('imap_password')->nullable()->after('imap_username');
            $table->string('imap_encryption')->nullable()->after('imap_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sending_identities', function (Blueprint $table) {
            $table->dropColumn([
                'send_mode',
                'imap_host',
                'imap_port',
                'imap_username',
                'imap_password',
                'imap_encryption',
            ]);
        });
    }
};
