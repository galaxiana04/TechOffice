<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKomatProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('komat_process', function (Blueprint $table) {
            $table->id();
            $table->string('komat_name');
            $table->unsignedBigInteger('komat_id')->nullable();
            $table->timestamps();

            $table->foreign('komat_id')
                ->references('id')
                ->on('newbomkomats')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('komat_process');
    }
}
