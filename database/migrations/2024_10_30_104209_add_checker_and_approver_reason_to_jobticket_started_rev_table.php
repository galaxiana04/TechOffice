<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('jobticket_started_rev', function (Blueprint $table) {
            $table->text('checker_reason')->nullable()->after('checker_status');
            $table->text('approver_reason')->nullable()->after('approver_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('jobticket_started_rev', function (Blueprint $table) {
            $table->dropColumn(['checker_reason', 'approver_reason']);
        });
    }
};
