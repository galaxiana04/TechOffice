<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cpm_task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpm_task_id')
                ->constrained('cpm_tasks')
                ->onDelete('cascade');
            $table->foreignId('predecessor_id')
                ->constrained('cpm_tasks')
                ->onDelete('cascade');
            $table->unique(['cpm_task_id', 'predecessor_id']);
            $table->timestamps(); // opsional
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpm_task_dependencies');
    }
};
