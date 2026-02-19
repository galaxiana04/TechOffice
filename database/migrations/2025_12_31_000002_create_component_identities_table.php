<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('component_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_operation_profile_id')
                ->constrained('project_operation_profiles')
                ->cascadeOnDelete();
            $table->string('component_l1')->nullable()->index();
            $table->string('component_l2')->nullable()->index();
            $table->string('component_l3')->nullable()->index();
            $table->string('component_l4')->nullable()->index();
            $table->boolean('is_repairable')
                ->default(true)
                ->comment('true = repairable (MTBF), false = non-repairable (MTTF)');
            $table->unique(['component_l1', 'component_l2', 'component_l3', 'component_l4', 'project_operation_profile_id'], 'unique_component_identity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('component_identities');
    }
};
