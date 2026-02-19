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
        Schema::rename('reliability_allocations', 'product_breakdown_structures');
    }

    public function down(): void
    {
        Schema::rename('product_breakdown_structures', 'reliability_allocations');
    }

};
