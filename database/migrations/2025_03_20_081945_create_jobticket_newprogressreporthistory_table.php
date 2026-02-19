<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('jobticket_newprogressreporthistory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jobticket_id');
            $table->unsignedBigInteger('newprogressreporthistory_id');
            $table->timestamps();

            // Tambahkan Foreign Key dengan nama custom (pendek)
            $table->foreign('jobticket_id', 'fk_jt_nprh_jt')
                ->references('id')->on('jobticket')
                ->onDelete('cascade');

            $table->foreign('newprogressreporthistory_id', 'fk_jt_nprh_nprh')
                ->references('id')->on('newprogressreporthistorys')
                ->onDelete('cascade');

            // Tambahkan UNIQUE constraint untuk memastikan pasangan tetap unik
            $table->unique(['jobticket_id', 'newprogressreporthistory_id'], 'uniq_jt_nprh');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobticket_newprogressreporthistory');
    }
};
