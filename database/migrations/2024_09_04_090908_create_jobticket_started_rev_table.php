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
        Schema::create('jobticket_started_rev', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jobticket_started_id');
            $table->string('revisionname')->nullable();
            $table->timestamp('start_time_run')->nullable();
            $table->timestamp('end_time_run')->nullable();
            $table->integer('total_elapsed_seconds')->nullable();
            $table->string('checker_status')->nullable();
            $table->string('approver_status')->nullable();
            $table->string('revision_status')->nullable();
            $table->timestamps();

            $table->foreign('jobticket_started_id')->references('id')->on('jobticket_started')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobticket_started_rev');
    }
};
