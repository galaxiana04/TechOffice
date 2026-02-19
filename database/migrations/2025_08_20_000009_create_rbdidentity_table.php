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
        Schema::create('rbdidentity', function (Blueprint $table) {
            $table->id();
            $table->string('componentname'); // nama komponen
            $table->unsignedBigInteger('proyek_type_id')->nullable();
            $table->unsignedBigInteger('time_interval')->default(1000); // default 1000 jam
            // foreign key ke project_types
            $table->float('temporary_reliability_value')->nullable(); // nilai reliabilitas sementara
            $table->string('diagram_url')->nullable(); // 👉 url diagram (nullable)
            $table->foreign('proyek_type_id')
                ->references('id')
                ->on('project_types')
                ->onDelete('set null');

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rbdidentity');
    }
};
