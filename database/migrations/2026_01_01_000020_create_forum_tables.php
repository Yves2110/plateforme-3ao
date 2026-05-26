<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category');
            $table->longText('body');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_validated')->default(true);
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();
        });

        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('forum_threads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('forum_replies')->nullOnDelete();
            $table->longText('body');
            $table->boolean('is_validated')->default(true);
            $table->boolean('is_solution')->default(false);
            $table->timestamps();
        });

        Schema::create('forum_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('forum_threads')->cascadeOnDelete();
            $table->string('question');
            $table->json('options');
            $table->timestamp('closes_at')->nullable();
            $table->timestamps();
        });

        Schema::create('forum_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('forum_polls')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('option_index');
            $table->timestamps();
            $table->unique(['poll_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_poll_votes');
        Schema::dropIfExists('forum_polls');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_threads');
    }
};
