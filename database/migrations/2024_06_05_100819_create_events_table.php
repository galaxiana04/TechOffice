<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('events');
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');

            $table->string('pic')->nullable();
            $table->string('agenda_desc')->nullable();
            $table->json('agenda_unit')->nullable();

            $table->dateTime('start');
            $table->dateTime('end')->nullable();
            $table->string('backgroundColor')->default('#0073b7');
            $table->string('borderColor')->default('#0073b7');
            $table->boolean('allDay')->default(false);
            $table->string('room')->nullable(); // Tambahkan kolom 'room'

            //zoom
            $table->string('password')->nullable();
            $table->string('join_url')->nullable();
            $table->string('idrapat')->nullable();

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('events')->onDelete('cascade'); // Menambahkan foreign key constraint

            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
