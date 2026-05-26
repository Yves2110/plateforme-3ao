<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formation_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('formation_modules')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['video', 'pdf', 'text', 'quiz', 'audio'])->default('text');
            $table->text('content')->nullable(); // Pour le texte ou lien vidéo
            $table->string('file_path')->nullable(); // Pour PDF/audio uploadés
            $table->string('video_url')->nullable(); // URL externe (YouTube, Vimeo)
            $table->integer('duration_minutes')->nullable(); // Durée estimée
            $table->integer('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['module_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_lessons');
    }
};
