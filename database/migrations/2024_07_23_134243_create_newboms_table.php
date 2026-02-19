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
        Schema::create('newboms', function (Blueprint $table) {
            $table->id();
            $table->string('BOMnumber')->unique();
            $table->string('unit')->nullable();
            $table->string('proyek_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('newboms');
    }
};
