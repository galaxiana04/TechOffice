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
        Schema::create('rollingstock_specs', function (Blueprint $table) {
            $table->id();
            $table->string('climate');
            $table->integer('average_temperature');
            $table->integer('lowest_temperature');
            $table->integer('highest_temperature');
            $table->integer('highest_operating_altitude');
            $table->integer('minimum_horizontal_curve_radius');
            $table->float('maximum_sustained_gradient_at_main_line');
            $table->float('maximum_sustained_gradient_at_depot');
            $table->foreignId('rollingstock_type_id')->constrained('rollingstock_types')->onDelete('cascade');
            $table->foreignId('rollingstock_designation_id')->constrained('rollingstock_designations')->onDelete('cascade');
            $table->float('axle_load_of_rollingstock');
            $table->integer('track_gauge');
            $table->integer('max_height_of_rollingstock');
            $table->integer('max_width_of_rollingstock');
            $table->integer('max_length_of_rollingstock_include_coupler');
            $table->integer('coupler_height');
            $table->string('coupler_type');
            $table->integer('distance_between_bogie_centers');
            $table->integer('distance_between_axle');
            $table->integer('wheel_diameter');
            $table->integer('floor_height_from_top_of_rail')->nullable();
            $table->integer('maximum_design_speed');
            $table->integer('maximum_operation_speed');
            $table->text('acceleration_rate')->nullable();
            $table->text('minimum_deceleration_rate')->nullable();
            $table->text('minimum_emergency_deceleration')->nullable();

            $table->text('bogie_type')->nullable();
            $table->text('brake_system')->nullable();
            $table->text('propulsion_system')->nullable();
            $table->text('suspension_system')->nullable();
            $table->text('carbody_material')->nullable();
            $table->text('air_conditioning_system')->nullable();
            $table->text('other_requirements')->nullable();
            $table->text('load_capacity')->nullable();




            // Perbaikan Foreign Key proyek_type_id
            $table->foreignId('proyek_type_id')
                ->nullable()
                ->constrained('project_types')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rollingstock_specs');
    }
};
