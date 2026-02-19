<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('newprogressreport_product_breakdown_structure', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_breakdown_structure_id');
            $table->unsignedBigInteger('newprogressreport_id');

            $table->foreign(
                'product_breakdown_structure_id',
                'fk_pbs_np_pbs_id'
            )
                ->references('id')
                ->on('product_breakdown_structures')
                ->cascadeOnDelete();

            $table->foreign(
                'newprogressreport_id',
                'fk_pbs_np_report_id'
            )
                ->references('id')
                ->on('newprogressreports')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['product_breakdown_structure_id', 'newprogressreport_id'],
                'pbs_progressreport_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newprogressreport_product_breakdown_structure');
    }
};
