<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('service_requests', 'technician_team_id')) {
                $table->foreignId('technician_team_id')
                    ->nullable()
                    ->after('service_request_subcategory_id')
                    ->constrained('technician_teams')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('service_requests', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable()->after('assigned_at');
            }

            if (! Schema::hasColumn('service_requests', 'on_the_way_at')) {
                $table->timestamp('on_the_way_at')->nullable()->after('scheduled_at');
            }

            if (! Schema::hasColumn('service_requests', 'estimated_arrival_minutes')) {
                $table->unsignedInteger('estimated_arrival_minutes')->nullable()->after('on_the_way_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'technician_team_id')) {
                $table->dropConstrainedForeignId('technician_team_id');
            }

            $drops = [];
            foreach (['scheduled_at', 'on_the_way_at', 'estimated_arrival_minutes'] as $column) {
                if (Schema::hasColumn('service_requests', $column)) {
                    $drops[] = $column;
                }
            }

            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }
};
