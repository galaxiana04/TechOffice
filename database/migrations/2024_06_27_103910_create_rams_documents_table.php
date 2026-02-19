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
        Schema::create('rams_documents', function (Blueprint $table) {
            $table->id();
            $table->string('documentname');
            $table->string('documentnumber');
            $table->string('proyek_type')->nullable();
            $table->json('ramsdocument_unit')->nullable();
            $table->timestamps();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('rams_documents');
    }
};
