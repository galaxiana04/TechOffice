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
        Schema::create('fta_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fta_identity_id')->constrained('fta_identities')->onDelete('cascade');
            $table->foreignId('fmeca_item_id')->constrained('fmeca_items')->onDelete('cascade'); // Relasi ke fmeca_items
            $table->string('name'); // Nama event, bisa diambil dari fmeca_item_name
            $table->double('failure_rate')->nullable(); // Diambil dari fmeca_items.failure_rate
            $table->string('source')->nullable(); // Sumber data, misalnya dari FMECA reference
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fta_events');
    }
};
