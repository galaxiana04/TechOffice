<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fmeca_identities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_type_id'); // relasi ke project_types
            $table->string('name'); // contoh: nama train/system/komponen
            $table->integer('train_yearly_hours')->default(0); // jam operasi tahunan
            $table->timestamps();

            // Foreign key
            $table->foreign('project_type_id')
                ->references('id')
                ->on('project_types')
                ->onDelete('cascade'); // hapus jika project_type dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fmeca_identities');
    }
};
