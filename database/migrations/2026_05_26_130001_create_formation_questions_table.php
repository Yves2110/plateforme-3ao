<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formation_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('formation_quizzes')->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['single_choice', 'multiple_choice', 'true_false', 'text'])->default('single_choice');
            $table->integer('points')->default(1); // Points pour cette question
            $table->text('explanation')->nullable(); // Explication de la réponse
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['quiz_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_questions');
    }
};
