<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHazardLogFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('hazardlogfeedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hazard_log_id')->constrained()->onDelete('cascade');
            $table->string('pic');
            $table->string('author');
            $table->string('level');
            $table->string('email');
            $table->text('comment')->nullable();
            $table->string('conditionoffile');
            $table->string('conditionoffile2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('hazardlogfeedbacks');
    }
}
