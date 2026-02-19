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
        Schema::create('new_memos', function (Blueprint $table) {
            $table->id();
            $table->string('documentname')->nullable();
            $table->string('documentnumber');
            $table->string('proyek_type')->nullable();
            $table->string('category')->nullable();
            $table->string('documentstatus')->nullable();
            $table->string('memokind')->nullable();
            $table->string('memoorigin')->nullable();
            $table->string('asliordummy')->nullable();
            $table->string('operator')->nullable();
            $table->json('project_pic')->nullable();
            $table->unsignedBigInteger('proyek_type_id')->nullable();
            // Jika menggunakan Laravel 8 ke atas, Anda bisa menggunakan ini untuk membuat foreign key constraint:
            $table->foreign('proyek_type_id')->references('id')->on('project_types')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('new_memos');
    }
};
