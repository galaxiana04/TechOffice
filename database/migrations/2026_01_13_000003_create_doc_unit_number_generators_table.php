<?php

// database/migrations/2026_01_13_000003_create_doc_unit_number_generators_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('doc_unit_number_generators', function (Blueprint $table) {
            $table->id();
            $table->string('unit_code')->unique(); // Contoh: MES, ENG
            $table->enum('unit', [
                'Product Engineering',
                'Mechanical Engineering System',
                'Electrical Engineering System',
                'Quality Engineering',
                'RAMS',
                'Sistem Mekanik',
                'Desain Interior',
                'Desain Bogie & Wagon',
                'Desain Carbody',
                'Desain Elektrik',
                'Preparation & Support',
                'Welding Technology',
                'Shop Drawing',
                'Teknologi Proses',
            ]);
            $table->string('description'); // Contoh: Mesin, Engineering
            $table->integer('prefix_number'); // Contoh: 1 → 1xx
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doc_unit_number_generators');
    }
};
