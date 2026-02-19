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
        Schema::create('fmecas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_type_id')->constrained()->onDelete('cascade')->comment('Foreign key to project_types table');
            $table->string('subsystemname')->comment('Name of the subsystem (e.g., Sheet1)');
            $table->string('issafetyorisreliability')->comment('Indicates if safety or reliability (e.g., safety, reliability)');
            $table->string('notifvalue')->comment('Notification value (e.g., Undesirable, Intolerable)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fmecas');
    }
};
