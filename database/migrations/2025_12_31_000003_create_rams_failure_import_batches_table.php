<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rams_failure_import_batches', function (Blueprint $table) {
            $table->id(); // batch_id (PRIMARY KEY)

            $table->string('source_file')->nullable()
                ->comment('Original Excel file name');

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('User who uploaded the batch');

            $table->timestamp('uploaded_at')
                ->useCurrent()
                ->comment('Batch upload time');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rams_failure_import_batches');
    }
};
