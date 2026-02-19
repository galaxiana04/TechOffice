<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fmeca_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fmeca_part_id'); // relasi ke fmeca_parts
            $table->integer('order')->nullable()->default(0);
            $table->string('item_ref')->unique();
            $table->string('subsystem')->nullable();
            $table->boolean('is_safety')->default(1)->comment('1 = Safety Risk, 0 = Reliability Risk');
            $table->string('item_name')->nullable(); // Item Name / Functional Identification
            $table->text('function')->nullable();
            $table->string('operational_mode')->nullable();
            $table->string('failure_mode')->nullable();
            $table->text('failure_causes')->nullable();
            $table->string('failure_base')->nullable();
            $table->decimal('ratio', 8, 2)->nullable();
            $table->double('failure_rate')->nullable();
            $table->integer('items_per_train')->nullable();
            $table->string('data_source')->nullable();
            $table->text('failure_effect_item')->nullable();
            $table->text('failure_effect_subsystem')->nullable();
            $table->text('failure_effect_system')->nullable();
            $table->string('reference')->nullable();

            // Risk hanya severity class (frequency & risk level dihitung logic)
            $table->string('safety_risk_severity_class')->nullable();
            $table->string('safety_risk_frequency')->nullable();
            $table->string('safety_risk_level')->nullable();


            $table->string('reliability_risk_severity_class')->nullable();
            $table->string('reliability_risk_frequency')->nullable();
            $table->string('reliability_risk_level')->nullable();

            $table->string('failure_detection_means')->nullable();
            $table->text('available_contingency')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->foreign('fmeca_part_id')
                ->references('id')
                ->on('fmeca_parts')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fmeca_items');
    }
};
