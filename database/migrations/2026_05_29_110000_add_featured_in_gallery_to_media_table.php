<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->boolean('featured_in_gallery')->default(false)->after('is_published');
            $table->unsignedSmallInteger('gallery_sort_order')->default(0)->after('featured_in_gallery');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['featured_in_gallery', 'gallery_sort_order']);
        });
    }
};
