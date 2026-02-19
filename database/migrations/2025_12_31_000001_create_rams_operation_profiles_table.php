<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rams_operation_profiles', function (Blueprint $table) {
            $table->id();

            // FK ke project_types
            $table->foreignId('project_type_id')
                ->constrained('project_types')
                ->cascadeOnDelete();

            // Parameter operasi
            $table->unsignedTinyInteger('daily_operation_hours')
                ->default(24)
                ->comment('Jam operasi per hari (1–24)');

            $table->unsignedTinyInteger('weekly_operation_days')
                ->default(7)
                ->comment('Hari operasi per minggu (1–7)');

            $table->timestamps();

            // 1 project = 1 operation profile (opsional, hapus jika ingin banyak profile)
            $table->unique('project_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rams_operation_profiles');
    }
};
