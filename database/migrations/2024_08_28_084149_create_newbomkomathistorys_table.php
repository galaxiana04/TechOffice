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
        Schema::create('newbomkomathistories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('newbomkomat_id'); // Reference to newbomkomats
            $table->string('kodematerial');
            $table->string('material');
            $table->string('status')->nullable();
            $table->string('rev')->nullable();
            $table->timestamps();

            $table->foreign('newbomkomat_id')->references('id')->on('newbomkomats')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('newbomkomathistories');
    }
};
