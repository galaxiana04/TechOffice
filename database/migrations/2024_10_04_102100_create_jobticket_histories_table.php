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
        Schema::create('jobticket_history', function (Blueprint $table) {
            $table->id();
            $table->string('historykind');
            $table->unsignedBigInteger('jobticket_identity_id'); // Foreign key harus unsignedBigInteger
            $table->unsignedBigInteger('newprogressreporthistory_id')->nullable();
            $table->unsignedBigInteger('newprogressreport_id')->nullable();
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Definisikan foreign key untuk relasi
            $table->foreign('jobticket_identity_id')->references('id')->on('jobticket_identity')->onDelete('cascade');
            $table->foreign('newprogressreporthistory_id')->references('id')->on('newprogressreporthistorys')->onDelete('set null');
            $table->foreign('newprogressreport_id')->references('id')->on('newprogressreports')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobticket_history');
    }
};
