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
        Schema::table('newprogressreports', function (Blueprint $table) {
            // Hapus kolom level
            $table->dropColumn('level');

            // Tambahkan foreign key relasi ke new_progress_reports_levels
            $table->foreignId('level_id')->nullable()
                ->constrained('new_progress_reports_levels')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            // Hapus foreign key dan kolom level_id
            $table->dropForeign(['level_id']);
            $table->dropColumn('level_id');

            // Tambahkan kembali kolom level
            $table->string('level')->nullable();
        });
    }
};
