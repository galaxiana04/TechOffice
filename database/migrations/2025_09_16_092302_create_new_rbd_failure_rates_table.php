<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_rbd_failure_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->decimal('failure_rate', 30, 30);
            $table->string('source', 255);
            $table->unique(['name', 'failure_rate', 'source']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_rbd_failure_rates');
    }
};
