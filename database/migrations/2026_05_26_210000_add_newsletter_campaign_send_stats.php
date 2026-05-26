<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_campaigns', function (Blueprint $table) {
            $table->unsignedInteger('sent_success_count')->default(0)->after('recipients_count');
            $table->unsignedInteger('sent_failed_count')->default(0)->after('sent_success_count');
            $table->text('last_error')->nullable()->after('sent_failed_count');
        });
    }

    public function down(): void
    {
        Schema::table('newsletter_campaigns', function (Blueprint $table) {
            $table->dropColumn(['sent_success_count', 'sent_failed_count', 'last_error']);
        });
    }
};
