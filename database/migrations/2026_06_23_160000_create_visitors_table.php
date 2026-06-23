<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_name');
            $table->string('visitor_phone');
            $table->date('visit_date');
            $table->time('estimated_arrival_time');
            $table->unsignedSmallInteger('guest_count')->default(1);
            $table->string('visit_purpose');
            $table->string('identity_photo_path')->nullable();
            $table->string('status')->default('Pending');
            $table->string('registration_source');
            $table->string('access_code', 120)->unique();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('access_card_number')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'visit_date']);
            $table->index(['resident_id', 'visit_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
