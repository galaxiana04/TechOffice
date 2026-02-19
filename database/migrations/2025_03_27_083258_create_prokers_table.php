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
        Schema::create('prokers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade'); // Relasi ke tabel units
            $table->string('name'); // Nama program kerja
            $table->string('proker_created_at'); // Format Month/YYYY
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prokers');
    }
};
