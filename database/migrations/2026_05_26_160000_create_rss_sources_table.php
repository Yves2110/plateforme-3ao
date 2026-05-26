<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rss_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_fetched_at')->nullable();
            $table->timestamps();
        });

        Schema::create('rss_feed_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rss_source_id')->constrained()->cascadeOnDelete();
            $table->string('guid')->nullable();
            $table->string('title');
            $table->string('link');
            $table->text('description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('actualite_id')->nullable()->constrained('actualites')->nullOnDelete();
            $table->timestamps();

            $table->unique(['rss_source_id', 'guid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rss_feed_items');
        Schema::dropIfExists('rss_sources');
    }
};
