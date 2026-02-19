<?php

// database/migrations/2026_01_13_000004_create_doc_number_generators_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('doc_number_generators', function (Blueprint $table) {
            $table->id();
            $table->string('full_doc_number')->unique();
            $table->string('name')->nullable();
            $table->string('bantuan_key')->index(); // prefix + unit_code + kind_code + product_code + year
            $table->integer('sequence_number'); // 1..99 per unit+jenis+produk+tahun
            $table->foreignId('kind_id')->constrained('doc_kind_number_generators');
            $table->foreignId('product_id')->constrained('doc_finish_product_number_generators');
            $table->foreignId('unit_id')->constrained('doc_unit_number_generators');
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('year', 4);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doc_number_generators');
    }
};
