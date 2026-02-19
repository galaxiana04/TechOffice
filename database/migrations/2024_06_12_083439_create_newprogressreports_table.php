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
        Schema::create('newprogressreports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newreport_id')->constrained('newreports')->onDelete('cascade');
            $table->foreignId('parent_revision_id')->nullable()->constrained('newprogressreports')->onDelete('cascade');
            $table->string('nodokumen');
            $table->string('namadokumen')->nullable();
            $table->string('level')->nullable();
            $table->string('drafter')->nullable();
            $table->string('checker')->nullable();
            $table->string('documentkind')->nullable();
            $table->string('deadlinerelease')->nullable();
            $table->string('realisasi')->nullable();
            $table->string('status')->nullable();
            $table->json('temporystatus')->nullable();
            $table->timestamps();
        });

        // Create revisions table
        Schema::create('revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newprogressreport_id')->constrained('newprogressreports')->onDelete('cascade');
            $table->string('revisionname');
            $table->timestamp('start_time_run')->nullable();
            $table->timestamp('end_time_run')->nullable();
            $table->string('revision_status')->default('belum divalidasi');
            $table->integer('total_elapsed_seconds')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revisions');
        Schema::dropIfExists('newprogressreports');
    }
};
