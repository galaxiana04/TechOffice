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
        Schema::table('jobticket_identity', function (Blueprint $table) {
            // Menambahkan kolom newprogressreportids yang bersifat nullable
            $table->json('newprogressreportids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobticket_identity', function (Blueprint $table) {
            // Menghapus kolom newprogressreportids
            $table->dropColumn('newprogressreportids');
        });
    }
};
