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
            // Hapus kolom 'deadlinerelease'
            $table->dropColumn('deadlinerelease');

            // Tambahkan kolom baru dengan tipe timestamp
            $table->timestamp('deadlinereleasedate')->nullable();
            $table->timestamp('startreleasedate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            // Tambahkan kembali kolom 'deadlinerelease' yang dihapus
            $table->string('deadlinerelease')->nullable();

            // Hapus kolom 'deadlinereleasedate' dan 'startreleasedate'
            $table->dropColumn('deadlinereleasedate');
            $table->dropColumn('startreleasedate');
        });
    }
};
