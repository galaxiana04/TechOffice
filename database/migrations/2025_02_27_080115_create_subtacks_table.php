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
        Schema::create('subtacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('number');
            $table->string('documentnumber');
            $table->unsignedBigInteger('tack_id'); // Foreign key ke TACK
            $table->foreign('tack_id')->references('id')->on('tacks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subtacks');
    }
};
