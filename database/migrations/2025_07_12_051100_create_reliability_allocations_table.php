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
        Schema::create('reliability_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_type_id')->constrained('project_types')->onDelete('cascade');
            $table->string('level1')->index(); // First level of hierarchy (e.g., R35-KRL-1)
            $table->string('level2')->nullable()->index(); // Second level (e.g., 1 for R35-KRL-1.1)
            $table->string('level3')->nullable()->index(); // Third level (e.g., 1 for R35-KRL-1.1.1)
            $table->string('system_level')->nullable(); // Descriptive name (e.g., Motor Bogie System)
            $table->integer('qty_per_ts')->nullable()->unsigned(); // Quantity per train set
            $table->integer('qty_per_system')->nullable()->unsigned(); // Quantity per system
            $table->integer('qty_per_subsystem')->nullable()->unsigned(); // Quantity per subsystem
            $table->integer('total_qty')->nullable()->unsigned(); // Total quantity
            $table->double('failure_rate', 15, 10)->nullable(); // Failure rate per component
            $table->double('failure_rate_total', 15, 10)->nullable(); // Total failure rate
            $table->string('source_note')->nullable(); // Data source
            $table->integer('average_speed_kph')->nullable()->unsigned(); // Average speed in kph
            $table->timestamps();
            $table->unique(['level1', 'level2', 'level3']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reliability_allocations');
    }
};