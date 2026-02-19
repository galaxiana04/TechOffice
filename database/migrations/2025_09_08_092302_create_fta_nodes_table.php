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
        Schema::create('fta_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fta_identity_id')->constrained('fta_identities')->onDelete('cascade');
            $table->string('type'); // 'and', 'or', 'basic_event'
            $table->string('event_name')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('fta_nodes')->onDelete('cascade');
            $table->foreignId('fta_event_id')->nullable()->constrained('fta_events')->onDelete('set null'); // Hanya untuk basic_event
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fta_nodes');
    }
};
