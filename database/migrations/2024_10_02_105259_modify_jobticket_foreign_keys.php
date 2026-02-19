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
        Schema::table('jobticket', function (Blueprint $table) {
            // Drop foreign keys terlebih dahulu
            $table->dropForeign(['drafter_id']);
            $table->dropForeign(['checker_id']);
            $table->dropForeign(['approver_id']);

            // Kemudian tambahkan kembali foreign key dengan onDelete('set null')
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
        Schema::table('jobticket', function (Blueprint $table) {
            // Drop foreign keys yang baru dibuat
            $table->dropForeign(['drafter_id']);
            $table->dropForeign(['checker_id']);
            $table->dropForeign(['approver_id']);

            // Kembalikan foreign key ke onDelete('cascade')
            $table->foreign('drafter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('checker_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
