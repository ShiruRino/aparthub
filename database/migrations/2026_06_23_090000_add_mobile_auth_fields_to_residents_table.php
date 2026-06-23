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
        Schema::table('residents', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('name');
            $table->string('mobile_no')->nullable()->unique()->after('email');
            $table->string('password')->nullable()->after('mobile_no');
            $table->date('contract_end_date')->nullable()->after('move_out_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropUnique(['mobile_no']);
            $table->dropColumn(['email', 'mobile_no', 'password', 'contract_end_date']);
        });
    }
};
