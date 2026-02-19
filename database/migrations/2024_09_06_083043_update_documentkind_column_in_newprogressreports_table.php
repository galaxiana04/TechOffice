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
            // Hapus kolom documentkind yang lama
            $table->dropColumn('documentkind');

            // Tambahkan kolom documentkind_id dan buat foreign key
            $table->foreignId('documentkind_id')->nullable()->constrained('newprogressreport_documentkind')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            // Hapus foreign key dan kolom documentkind_id
            $table->dropForeign(['documentkind_id']);
            $table->dropColumn('documentkind_id');

            // Kembalikan kolom documentkind yang lama
            $table->string('documentkind')->nullable();
        });
    }
};
