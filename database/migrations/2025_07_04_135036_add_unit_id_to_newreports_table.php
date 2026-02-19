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
        Schema::table('newreports', function (Blueprint $table) {
            $table->foreignId('unit_id')
                ->nullable() // Biarkan NULL untuk data lama
                ->constrained('newprogressreport_units') // Referensi ke tabel unit
                ->nullOnDelete() // Jika unit dihapus, set NULL
                ->after('unit'); // Letakkan setelah kolom id
            $table->unique(
                ['proyek_type_id', 'proyek_type', 'unit', 'unit_id'],
                'unique_report'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newreports', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });
    }
};
