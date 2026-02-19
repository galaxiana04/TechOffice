<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('newprogressreport_product_breakdown_structure', function (Blueprint $table) {
            $table->renameColumn('reliability_allocation_id', 'product_breakdown_structure_id');
        });
    }

    public function down(): void
    {
        Schema::table('newprogressreport_product_breakdown_structure', function (Blueprint $table) {
            $table->renameColumn('product_breakdown_structure_id', 'reliability_allocation_id');
        });
    }

};
