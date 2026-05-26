<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('ONG');
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->timestamps();
        });

        Schema::create('actor_themes', function (Blueprint $table) {
            $table->foreignId('actor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['actor_id', 'tag_id']);
        });

        Schema::create('actor_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id_from')->constrained('actors')->cascadeOnDelete();
            $table->foreignId('actor_id_to')->constrained('actors')->cascadeOnDelete();
            $table->string('relation_type')->nullable();
            $table->string('project_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actor_links');
        Schema::dropIfExists('actor_themes');
        Schema::dropIfExists('actors');
    }
};
