<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->foreignId('service_request_category_id')
                ->nullable()
                ->after('resident_id')
                ->constrained('service_request_categories')
                ->nullOnDelete();
            $table->foreignId('service_request_subcategory_id')
                ->nullable()
                ->after('service_request_category_id')
                ->constrained('service_request_subcategories')
                ->nullOnDelete();
            $table->unsignedInteger('sla_target_minutes')->nullable()->after('source');
            $table->timestamp('sla_due_at')->nullable()->after('sla_target_minutes');
            $table->timestamp('assigned_at')->nullable()->after('assigned_to');
            $table->timestamp('in_progress_at')->nullable()->after('assigned_at');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_request_subcategory_id');
            $table->dropConstrainedForeignId('service_request_category_id');
            $table->dropColumn([
                'sla_target_minutes',
                'sla_due_at',
                'assigned_at',
                'in_progress_at',
            ]);
        });
    }
};
