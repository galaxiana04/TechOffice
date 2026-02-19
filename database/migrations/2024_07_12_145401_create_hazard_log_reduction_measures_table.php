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
        Schema::create('hazard_log_reduction_measures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hazard_log_id')->constrained()->onDelete('cascade');
            $table->string('unit_name');
            $table->string('reduction_measure');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hazard_log_reduction_measures');
    }
};
