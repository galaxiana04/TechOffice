<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_rbd_instances', function (Blueprint $table) {
            $table->id();
            $table->string('componentname');
            $table->unsignedBigInteger('proyek_type_id')->nullable();
            $table->unsignedBigInteger('time_interval')->default(1000);
            $table->decimal('temporary_reliability_value', 30, 20)->nullable();
            // Kolom baru pakai decimal
            $table->decimal('temporary_failure_rate_value', 30, 15)->nullable();
            $table->string('diagram_url')->nullable();
            $table->foreign('proyek_type_id')
                ->references('id')
                ->on('project_types')
                ->onDelete('set null');
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->onDelete('set null');
            // Ekspresi simbolik (string panjang)
            $table->text('r_t_symbolic')->nullable();
            $table->text('hazard_rate_expression')->nullable();
            $table->text('frequency_expression')->nullable();
            $table->text('t_expression')->nullable();

            // Nilai numerik besar: t_value (bisa sampai 10+ digit)
            $table->decimal('t_value', 20, 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_rbd_instances');
    }
};
