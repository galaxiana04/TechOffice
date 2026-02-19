<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('hazard_logs', function (Blueprint $table) {
            $table->id();
            $table->string('hazard_ref');
            $table->string('proyek_type')->nullable();
            $table->json('hazard_unit')->nullable();

            $table->string('operating_mode')->nullable();
            $table->string('system')->nullable();
            $table->string('hazard')->nullable();
            $table->string('hazard_cause')->nullable();
            $table->string('accident')->nullable();
            $table->string('IF')->nullable();
            $table->string('IS')->nullable();
            $table->string('risk_reduction_measures')->nullable();
            $table->string('resolution_status')->nullable();
            $table->string('source')->nullable();
            $table->string('haz_owner')->nullable();
            $table->string('hazard_status')->nullable();
            $table->string('date_updated')->nullable();
            $table->string('RF')->nullable();
            $table->string('RS')->nullable();
            $table->string('RR')->nullable();
            $table->string('IR')->nullable();
            $table->string('verification_evidence_reference')->nullable();
            $table->string('validation_evidence_reference')->nullable();
            $table->string('comments')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('hazard_logs');
    }
};
