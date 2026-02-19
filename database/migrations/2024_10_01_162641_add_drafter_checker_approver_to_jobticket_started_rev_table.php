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
            $table->unsignedBigInteger('drafter_id')->nullable();
            $table->unsignedBigInteger('checker_id')->nullable();
            $table->unsignedBigInteger('approver_id')->nullable();

            $table->foreign('drafter_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('checker_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('jobticket_started_rev', function (Blueprint $table) {
            $table->dropForeign(['drafter_id']);
            $table->dropForeign(['checker_id']);
            $table->dropForeign(['approver_id']);
            $table->dropColumn(['drafter_id', 'checker_id', 'approver_id']);
        });
    }
};
