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
        Schema::create('jobticket_part', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('proyek_type_id')->nullable();
            // Menambahkan foreign key constraint
            $table->foreign('proyek_type_id')->references('id')->on('project_types')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('jobticket_part');
    }
};
