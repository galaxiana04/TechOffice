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
        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->text('description');
            $table->string('password'); // Tambahkan kolom password
            $table->unsignedBigInteger('forumable_id')->nullable(); // ID dari model pemilik (Problem atau Discussion)
            $table->string('forumable_type')->nullable(); // Nama class dari model pemilik (Problem atau Discussion)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('forums');
    }
};
