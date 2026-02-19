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
        Schema::create('justi_memos', function (Blueprint $table) {
            $table->id();  // Primary key, auto-incremented
            $table->string('documentname');  // Nomor dokumen dengan constraint unique
            $table->string('documentnumber');  // Nomor dokumen dengan constraint unique
            $table->unsignedBigInteger('proyek_type_id')->nullable();  // Foreign key ke project_types, nullable
            $table->foreign('proyek_type_id')
                  ->references('id')
                  ->on('project_types')
                  ->onDelete('restrict');  // Restrict jika referensi dihapus
            $table->string('documentstatus')->nullable();  // Status dokumen
            $table->json('project_pic_id')->nullable();  // Array JSON dengan id dari units
            $table->timestamps();  // Kolom created_at dan updated_at otomatis
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('justi_memos');
    }
};
