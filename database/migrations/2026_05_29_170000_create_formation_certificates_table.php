<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formation_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('formation_enrollments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->string('certificate_number')->unique();
            $table->string('learner_name');
            $table->string('learner_email');
            $table->string('learner_organization')->nullable();
            $table->string('formation_title');
            $table->timestamp('issued_at');
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'formation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_certificates');
    }
};
