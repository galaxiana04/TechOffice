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
        Schema::create('jobticket_started_rev_reasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jobticket_started_rev_id');
            $table->string('rule');
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('jobticket_started_rev_id')->references('id')->on('jobticket_started_rev')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('jobticket_started_rev_reasons');
    }
};
