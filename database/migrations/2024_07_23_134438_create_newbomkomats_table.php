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
        Schema::create('newbomkomats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('newbom_id');
            $table->string('kodematerial');
            $table->string('material');
            $table->string('status')->nullable();
            $table->string('rev')->nullable();
            $table->timestamps();
            $table->foreign('newbom_id')->references('id')->on('newboms')->onDelete('cascade');
            $table->unique(['newbom_id', 'kodematerial'], 'unique_newbom_material');
        });
    }

    public function down()
    {
        Schema::dropIfExists('newbomkomats');
    }
};
