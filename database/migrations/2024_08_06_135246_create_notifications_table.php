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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notifmessage_id');
            $table->string('notifmessage_type');
            $table->unsignedBigInteger('idunit')->nullable(); 
            $table->string('status')->nullable();
            $table->string('infostatus')->nullable();
            $table->json('notifarray')->nullable();
            $table->timestamps();
            
            // Optional: tambahkan foreign key untuk idunit jika diperlukan
            $table->foreign('idunit')->references('id')->on('units')->onDelete('set null');

        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
