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
        Schema::create('jobticket_started', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jobticket_id');
            $table->timestamp('start_time_first')->nullable();
            $table->timestamp('start_time_run')->nullable();
            $table->timestamp('pause_time_run')->nullable();
            $table->integer('total_elapsed_seconds')->nullable();
            $table->string('statusrevisi')->nullable();
            $table->string('revisionlast')->nullable();
            $table->timestamps();

            $table->foreign('jobticket_id')->references('id')->on('jobticket')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobticket_started');
    }
};
