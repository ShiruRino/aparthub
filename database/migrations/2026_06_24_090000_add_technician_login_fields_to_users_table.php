<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->unique()->after('username');
            }

            if (! Schema::hasColumn('users', 'mobile_no')) {
                $table->string('mobile_no')->nullable()->unique()->after('email');
            }

            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('mobile_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('users', 'mobile_no')) {
                $table->dropUnique('users_mobile_no_unique');
                $table->dropColumn('mobile_no');
            }

            if (Schema::hasColumn('users', 'email')) {
                $table->dropUnique('users_email_unique');
                $table->dropColumn('email');
            }
        });
    }
};
