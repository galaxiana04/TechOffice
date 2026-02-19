<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewbomkomatNewprogressreportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newbomkomat_newprogressreport', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('newbomkomat_id'); // Foreign key ke tabel newbomkomats
            $table->unsignedBigInteger('newprogressreport_id'); // Foreign key ke tabel newprogressreports
            $table->timestamps();

            // Tambahkan foreign key constraints
            $table->foreign('newbomkomat_id')
                ->references('id')
                ->on('newbomkomats')
                ->onDelete('restrict'); // Cegah penghapusan terkait

            $table->foreign('newprogressreport_id')
                ->references('id')
                ->on('newprogressreports')
                ->onDelete('restrict'); // Cegah penghapusan terkait

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('newbomkomat_newprogressreport');
    }
}
