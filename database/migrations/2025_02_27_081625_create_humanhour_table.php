<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('human_hours', function (Blueprint $table) {
            $table->id();
            $table->integer('month'); // Menggunakan angka 1-12 untuk bulan
            $table->integer('year');
            $table->unsignedInteger('humanhours')->default(0); // Kolom untuk menyimpan humanhour
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('human_hours');
    }
};
