<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeColumnsNullableInKatalogKomatTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('katalog_komat', function (Blueprint $table) {
            $table->string('deskripsi')->nullable()->change();
            $table->string('spesifikasi')->nullable()->change();
            $table->string('UoM')->nullable()->change();
            $table->integer('stokUUekpedisi')->nullable()->change();
            $table->integer('stokUUgudang')->nullable()->change();
            $table->integer('stokprojectekpedisi')->nullable()->change();
            $table->integer('stokprojectgudang')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('katalog_komat', function (Blueprint $table) {
            $table->string('deskripsi')->nullable(false)->change();
            $table->string('spesifikasi')->nullable(false)->change();
            $table->string('UoM')->nullable(false)->change();
            $table->integer('stokUUekpedisi')->nullable(false)->change();
            $table->integer('stokUUgudang')->nullable(false)->change();
            $table->integer('stokprojectekpedisi')->nullable(false)->change();
            $table->integer('stokprojectgudang')->nullable(false)->change();
        });
    }
}
