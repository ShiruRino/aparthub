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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('tower');
            $table->unsignedTinyInteger('floor');
            $table->string('unit_type');
            $table->string('occupancy_status')->default('Kosong');
            $table->string('payment_status')->default('Belum Lunas');
            $table->string('thumbnail_tone')->default('default');
            $table->timestamps();
        });

        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('resident_type');
            $table->string('status')->default('Menunggu Approval');
            $table->date('move_in_date')->nullable();
            $table->date('move_out_date')->nullable();
            $table->string('avatar_tone')->default('default');
            $table->timestamps();
        });

        Schema::create('resident_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('relationship');
            $table->date('birth_date')->nullable();
            $table->string('access_status')->default('Aktif');
            $table->timestamps();
        });

        Schema::create('resident_move_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('resident_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('request_type');
            $table->date('scheduled_date')->nullable();
            $table->string('status')->default('Menunggu Approval');
            $table->string('status_note')->nullable();
            $table->timestamps();
        });

        Schema::create('resident_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('plate_number')->unique();
            $table->string('vehicle_type');
            $table->string('owner_name');
            $table->string('make_model');
            $table->string('parking_status')->default('Aktif');
            $table->string('slot_label')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resident_vehicles');
        Schema::dropIfExists('resident_move_requests');
        Schema::dropIfExists('resident_family_members');
        Schema::dropIfExists('residents');
        Schema::dropIfExists('units');
    }
};
