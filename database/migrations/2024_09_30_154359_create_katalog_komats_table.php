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
        Schema::create('katalog_komat', function (Blueprint $table) {
            $table->id();
            $table->string('kodematerial');
            $table->string('deskripsi');
            $table->string('spesifikasi');
            $table->string('UoM');
            $table->integer('stokUUekpedisi');
            $table->integer('stokUUgudang');
            $table->integer('stokprojectekpedisi');
            $table->integer('stokprojectgudang');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('katalog_komat');
    }
};
