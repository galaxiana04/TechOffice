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
        Schema::rename('newprogressreport_reliability_allocation', 'newprogressreport_product_breakdown_structure');
    }

    public function down(): void
    {
        Schema::rename('newprogressreport_product_breakdown_structure', 'newprogressreport_reliability_allocation');
    }

};
