<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('new_rbd_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->onDelete('set null');
            $table->string('name');
            $table->unsignedBigInteger('proyek_type_id')->nullable();
            $table->text('description')->nullable();
            $table->foreign('proyek_type_id')
                ->references('id')
                ->on('project_types')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_rbd_models');
    }
};
