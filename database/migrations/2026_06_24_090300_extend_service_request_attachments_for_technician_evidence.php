<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_request_attachments', function (Blueprint $table) {
            if (! Schema::hasColumn('service_request_attachments', 'attachment_type')) {
                $table->string('attachment_type')->default('resident_supporting')->after('file_size');
            }

            if (! Schema::hasColumn('service_request_attachments', 'uploaded_by_user_id')) {
                $table->foreignId('uploaded_by_user_id')
                    ->nullable()
                    ->after('attachment_type')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        DB::table('service_request_attachments')
            ->whereNull('attachment_type')
            ->update(['attachment_type' => 'resident_supporting']);
    }

    public function down(): void
    {
        Schema::table('service_request_attachments', function (Blueprint $table) {
            if (Schema::hasColumn('service_request_attachments', 'uploaded_by_user_id')) {
                $table->dropConstrainedForeignId('uploaded_by_user_id');
            }

            if (Schema::hasColumn('service_request_attachments', 'attachment_type')) {
                $table->dropColumn('attachment_type');
            }
        });
    }
};
