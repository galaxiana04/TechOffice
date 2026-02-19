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
        Schema::create('proker_monthly', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proker_id')->constrained('prokers')->onDelete('cascade'); // Relasi ke tabel prokers
            $table->string('date'); // Format Month/YYYY
            $table->integer('percentage')->default(0); // Persentase progres
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proker_monthly');
    }
};
