<?php

// database/migrations/2026_01_13_000002_create_doc_finish_product_number_generators_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('doc_finish_product_number_generators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_type_id')
                ->constrained('project_types')
                ->onDelete('cascade');
            $table->string('finish_product_code'); // Contoh: H1005MB051
            $table->string('year', 4);
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamps();

            // Unique constraint untuk kombinasi project_type_id + finish_product_code
            $table->unique(['project_type_id', 'finish_product_code'], 'unique_project_finish');
        });
    }

    public function down()
    {
        Schema::dropIfExists('doc_finish_product_number_generators');
    }
};
