<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDeskripsiAndSpesifikasiToTextInKatalogKomatTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('katalog_komat', function (Blueprint $table) {
            // Mengubah tipe kolom deskripsi dan spesifikasi menjadi TEXT
            $table->text('deskripsi')->nullable()->change();
            $table->text('spesifikasi')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('katalog_komat', function (Blueprint $table) {
            // Mengembalikan tipe kolom deskripsi dan spesifikasi menjadi STRING (VARCHAR)
            $table->string('deskripsi', 255)->nullable()->change();
            $table->string('spesifikasi', 255)->nullable()->change();
        });
    }
}
