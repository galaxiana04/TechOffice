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
        // Gunakan if !hasTable agar tidak error "table already exists" lagi
        if (!Schema::hasTable('product_breakdown_structures')) {
            Schema::create('product_breakdown_structures', function (Blueprint $table) {
                $table->id();

                $table->foreignId('project_type_id')
                    ->constrained('project_types')
                    ->cascadeOnDelete();

                $table->string('product')->nullable();

                // PERBAIKAN: Tambahkan limit 150 agar unique index tidak terlalu panjang
                $table->string('level1', 150)->index();
                $table->string('level2', 150)->nullable()->index();
                $table->string('level3', 150)->nullable()->index();
                $table->string('level4', 150)->nullable()->index();

                $table->unsignedInteger('qty_per_ts')->nullable();
                $table->unsignedInteger('qty_per_system')->nullable();
                $table->unsignedInteger('qty_per_subsystem')->nullable();
                $table->unsignedInteger('total_qty')->nullable();

                $table->double('failure_rate', 15, 10)->nullable();
                $table->double('failure_rate_total', 15, 10)->nullable();

                $table->string('source_note')->nullable();
                $table->unsignedInteger('average_speed_kph')->nullable();

                $table->timestamps();

                // Index unik sekarang aman karena total bytes (150*4*4) < 3072
                $table->unique(
                    ['project_type_id', 'level1', 'level2', 'level3', 'level4'],
                    'pbs_unique_hierarchy'
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_breakdown_structures');
    }
};