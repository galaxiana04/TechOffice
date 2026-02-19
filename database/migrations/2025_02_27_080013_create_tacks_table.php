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
        Schema::create('tacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('number');
            $table->unsignedBigInteger('proyek_type_id')->nullable(); // Foreign key ke project_types
            $table->foreign('proyek_type_id')->references('id')->on('project_types')->onDelete('set null');
            $table->unsignedBigInteger('tack_phase_id')->nullable();
            $table->foreign('tack_phase_id')->references('id')->on('tack_phases')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tacks');
    }
};
