<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('newprogressreport_reliability_allocation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reliability_allocation_id');
            $table->unsignedBigInteger('newprogressreport_id');
            $table->timestamps();

            // Custom constraint name (shorter)
            $table->foreign('reliability_allocation_id', 'fk_allocation')
                ->references('id')
                ->on('reliability_allocations')
                ->onDelete('cascade');

            $table->foreign('newprogressreport_id', 'fk_progressreport')
                ->references('id')
                ->on('newprogressreports')
                ->onDelete('cascade');

            $table->unique(['reliability_allocation_id', 'newprogressreport_id'], 'allocation_progressreport_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newprogressreport_reliability_allocation');
    }
};
