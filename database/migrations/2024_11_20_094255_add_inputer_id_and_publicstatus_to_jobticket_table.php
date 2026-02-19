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
            // Menambahkan kolom inputer_id
            $table->unsignedBigInteger('inputer_id')->nullable();

            // Menambahkan kolom publicstatus
            $table->string('publicstatus')->nullable();

            // Menambahkan foreign key untuk inputer_id jika diperlukan
            $table->foreign('inputer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('jobticket', function (Blueprint $table) {
            // Hapus foreign key dulu
            $table->dropForeign(['inputer_id']);

            // Baru hapus kolomnya
            $table->dropColumn('inputer_id');
            $table->dropColumn('publicstatus');
        });
    }
};
