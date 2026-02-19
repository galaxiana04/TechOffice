<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rams_component_identities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rams_operation_profile_id')
                ->constrained('rams_operation_profiles')
                ->cascadeOnDelete();

            // Batasi panjang karakter (misal: 150) agar index tidak terlalu panjang
            $table->string('component_l1', 150)->nullable()->index();
            $table->string('component_l2', 150)->nullable()->index();
            $table->string('component_l3', 150)->nullable()->index();
            $table->string('component_l4', 150)->nullable()->index();

            $table->boolean('is_repairable')
                ->default(true)
                ->comment('true = repairable (MTBF), false = non-repairable (MTTF)');
            
            $table->unsignedInteger('installed_quantity')->nullable()
                ->comment('Jumlah komponen terpasang sebenarnya');

            // Unique index sekarang aman karena panjang total kolom string berkurang
            $table->unique([
                'component_l1',
                'component_l2',
                'component_l3',
                'component_l4',
                'rams_operation_profile_id'
            ], 'unique_rams_component_identity');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rams_component_identities');
    }
};