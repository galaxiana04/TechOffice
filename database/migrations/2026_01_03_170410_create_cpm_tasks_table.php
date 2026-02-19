<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cpm_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpm_project_id') // FK ke cpm_projects
                ->constrained('cpm_projects')
                ->onDelete('cascade');
            $table->string('code'); // e.g., CD-SE-001
            $table->string('name');
            $table->string('pic')->nullable();              // PIC (Sys Eng, Rend Eng, MTPR, dll)
            $table->integer('sequence')->nullable();        // Sequence (untuk urutan tampilan jika perlu)
            $table->decimal('duration', 8, 2)->default(0.00);
            $table->string('classification')->nullable();   // Classification (e.g., Milestone (Baseline), Concept Phase, dll)

            // Tanggal kalender
            $table->date('actual_start_date')->nullable();
            $table->date('actual_finish_date')->nullable();

            // perhitungan CPM
            $table->integer('es')->nullable();
            $table->integer('ef')->nullable();
            $table->integer('ls')->nullable();
            $table->integer('lf')->nullable();
            $table->integer('slack')->nullable();
            $table->boolean('is_critical')->default(false);
            $table->boolean('is_done')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpm_tasks');
    }
};
