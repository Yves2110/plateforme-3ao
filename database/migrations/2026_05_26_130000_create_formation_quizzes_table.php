<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formation_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('formation_lessons')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('passing_score')->default(70); // Score minimum pour réussir (en %)
            $table->integer('time_limit_minutes')->nullable(); // Temps limite optionnel
            $table->integer('max_attempts')->default(3); // Nombre max de tentatives
            $table->boolean('is_published')->default(false);
            $table->boolean('show_correct_answers')->default(true); // Montrer les bonnes réponses après
            $table->timestamps();

            $table->index(['lesson_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_quizzes');
    }
};
