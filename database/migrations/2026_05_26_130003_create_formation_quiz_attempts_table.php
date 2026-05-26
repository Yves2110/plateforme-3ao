<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formation_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('formation_quizzes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('score')->default(0); // Score obtenu (en points)
            $table->integer('max_score')->default(0); // Score maximum possible
            $table->decimal('percentage', 5, 2)->default(0); // Pourcentage (0-100)
            $table->boolean('is_passed')->default(false); // Réussi ?
            $table->integer('attempt_number')->default(1); // Numéro de tentative
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->json('answers')->nullable(); // Réponses données {question_id: answer_id}
            $table->timestamps();

            $table->index(['quiz_id', 'user_id', 'attempt_number']);
            $table->index(['user_id', 'is_passed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_quiz_attempts');
    }
};
