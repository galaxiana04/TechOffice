<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cpm_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_type_id')
                ->constrained('project_types')
                ->onDelete('cascade');
            $table->string('description')->nullable(); // deskripsi CPM project
            // ⬇️ START DATE PROYEK
            $table->date('project_start_date');
            $table->unsignedInteger('project_duration')->default(0);
            $table->unsignedInteger('total_tasks')->default(0);
            $table->unsignedInteger('critical_tasks')->default(0);
            $table->timestamp('cpm_calculated_at')->nullable();
            $table->timestamps();

            // 1 ProjectType hanya punya 1 CPM Project (opsional, bisa dihapus jika ingin banyak)
            $table->unique('project_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpm_projects');
    }
};
