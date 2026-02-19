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
        Schema::create('fta_identities', function (Blueprint $table) {
            $table->id();
            $table->string('componentname');
            $table->foreignId('proyek_type_id')->nullable()->constrained('project_types')->onDelete('set null');
            $table->double('cfi')->nullable(); // Menyimpan probabilitas kegagalan top event
            $table->string('diagram_url')->nullable(); // Opsional untuk URL diagram eksternal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fta_identities');
    }
};
