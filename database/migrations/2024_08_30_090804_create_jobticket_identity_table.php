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
        Schema::create('jobticket_identity', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jobticket_part_id');
            $table->unsignedBigInteger('jobticket_documentkind_id')->nullable();
            $table->string('documentnumber');
            $table->timestamps();
            $table->foreign('jobticket_documentkind_id')->references('id')->on('jobticket_documentkind')->onDelete(null);
            $table->foreign('jobticket_part_id')->references('id')->on('jobticket_part')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('jobticket_identity');
    }
};
