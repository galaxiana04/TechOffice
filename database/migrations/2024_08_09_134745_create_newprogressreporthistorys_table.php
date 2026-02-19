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
        Schema::create('newprogressreporthistorys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newprogressreport_id')->constrained('newprogressreports')->onDelete('cascade');
            $table->string('nodokumen');
            $table->string('namadokumen')->nullable();
            $table->string('level')->nullable();
            $table->string('drafter')->nullable();
            $table->string('checker')->nullable();
            $table->string('documentkind')->nullable();
            $table->string('realisasi')->nullable();
            $table->string('deadlinerelease')->nullable();
            $table->string('status')->nullable();
            $table->string('rev')->nullable();
            $table->json('temporystatus')->nullable();

            $table->unique(
                ['newprogressreport_id', 'nodokumen', 'rev'],
                'nprh_unique_document_revision'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newprogressreporthistorys');
    }
};
