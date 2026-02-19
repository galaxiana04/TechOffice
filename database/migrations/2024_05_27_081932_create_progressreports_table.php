<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressreportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('progressreports');
        Schema::create('progressreports', function (Blueprint $table) {
            $table->id();
            $table->string('progressreportname');
            $table->string('proyek_type')->nullable();
            $table->string('status')->nullable();
            $table->string('linkspreadsheet')->nullable();
            $table->string('linkscript')->nullable();
            $table->json('revisi')->nullable();
            $table->json('timeline')->nullable();
            $table->timestamps(); // Tambahan untuk menambahkan kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('progressreports');
    }
}
