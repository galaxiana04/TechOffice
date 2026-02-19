<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cpm_task_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpm_task_id')->constrained('cpm_tasks')->onDelete('cascade');
            $table->foreignId('progress_report_id')->constrained('newprogressreports')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpm_task_progress');
    }
};
