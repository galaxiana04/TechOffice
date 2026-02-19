<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('unit'); // Nama unit
            $table->json('data');   // Data JSON
            $table->string('view_name'); // Nama view yang akan digunakan
            $table->date('date');   // Tanggal spesifik untuk laporan
            $table->timestamps();   // created_at dan updated_at

            // Menambahkan unique constraint untuk pasangan unit dan date
            $table->unique(['unit', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_snapshots');
    }
};
