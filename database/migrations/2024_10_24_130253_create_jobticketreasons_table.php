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
        Schema::create('jobticketreasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jobticket_id');
            $table->string('reason')->nullable();
            $table->string('kind')->nullable();
            $table->unsignedBigInteger('kind_id')->nullable();
            $table->string('kind_type')->nullable();
            $table->timestamp('start')->nullable(); // Menambahkan kolom start
            $table->timestamp('end')->nullable();   // Menambahkan kolom end
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('jobticket_id')->references('id')->on('jobticket')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobticketreasons');
    }
};
