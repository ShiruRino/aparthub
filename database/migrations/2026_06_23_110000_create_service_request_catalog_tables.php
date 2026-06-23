<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_request_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('service_request_subcategories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_request_category_id');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('low_sla_minutes');
            $table->unsignedInteger('medium_sla_minutes');
            $table->unsignedInteger('high_sla_minutes');
            $table->unsignedInteger('emergency_sla_minutes');
            $table->timestamps();

            $table->foreign('service_request_category_id', 'sr_subcats_category_fk')
                ->references('id')
                ->on('service_request_categories')
                ->cascadeOnDelete();
            $table->unique(['service_request_category_id', 'name'], 'service_request_subcategories_category_name_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_request_subcategories');
        Schema::dropIfExists('service_request_categories');
    }
};
