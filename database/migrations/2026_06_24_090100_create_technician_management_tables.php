<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technician_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('technician_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('profile_photo_path')->nullable();
            $table->json('skills')->nullable();
            $table->json('certifications')->nullable();
            $table->boolean('notification_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('technician_team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['technician_team_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_team_user');
        Schema::dropIfExists('technician_profiles');
        Schema::dropIfExists('technician_teams');
    }
};
