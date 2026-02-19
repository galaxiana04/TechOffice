<?php

// database/migrations/2026_01_13_000001_create_doc_kind_number_generators_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('doc_kind_number_generators', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Contoh: VED, SPT
            $table->string('description'); // Contoh: Verifikasi Desain
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doc_kind_number_generators');
    }
};
