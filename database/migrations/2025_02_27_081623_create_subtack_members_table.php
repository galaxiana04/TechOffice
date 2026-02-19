<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('subtack_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('subtack_id'); // Foreign key ke subtacks
            $table->foreign('subtack_id')->references('id')->on('subtacks')->onDelete('cascade');
            $table->timestamps();


        });
    }

    public function down()
    {
        Schema::dropIfExists('subtack_members');
    }
};
