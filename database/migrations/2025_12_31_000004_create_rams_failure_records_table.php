<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rams_failure_records', function (Blueprint $table) {
            $table->id();
            // FK KE BATCH (INI KUNCI DESAIN)
            $table->foreignId('import_batch_id')
                ->nullable()
                ->constrained('rams_failure_import_batches')
                ->nullOnDelete();
            $table->foreignId('rams_component_identity_id')
                ->constrained('rams_component_identities')
                ->onDelete('cascade');
            $table->date('start_date');
            $table->date('failure_date');
            $table->time('failure_time');
            $table->integer('workdays')->nullable();
            $table->decimal('ttf_hours', 12, 2);
            $table->string('source_file')->nullable();
            $table->string('service_type')->nullable();
            $table->boolean('is_new')
                ->default(false)
                ->comment('true = new component, false = used component');
            // Additional columns as needed
            $table->string('trainset')->nullable();       // Train set
            $table->string('train_no')->nullable();       // Train number
            $table->string('car_type')->nullable();       // Car type
            $table->string('relation')->nullable();       // Relation / Connection
            $table->text('problemdescription')->nullable();        // Finding / Observation
            $table->text('solution')->nullable();       // Solution
            $table->text('cause_classification')->nullable();       // Cause Classification
            $table->string('supporting_link')->nullable(); // Supporting Link
            $table->timestamps();

            // BERI NAMA PENDEK UNTUK UNIQUE CONSTRAINT
            $table->unique(
                ['rams_component_identity_id', 'start_date', 'failure_date', 'failure_time'],
                'fr_unique_event'  // nama pendek, maksimal 64 char
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rams_failure_records');
    }
};
