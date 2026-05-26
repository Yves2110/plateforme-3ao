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
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type')->default('atelier'); // atelier, cours, webinaire, certification
            $table->string('organizer')->nullable();
            $table->string('country')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_online')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('duration')->nullable(); // ex: "3 jours", "6 semaines"
            $table->text('description')->nullable();
            $table->text('objectives')->nullable();
            $table->string('audience')->nullable();
            $table->string('language')->default('fr');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('registration_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
