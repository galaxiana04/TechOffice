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
        Schema::table('newprogressreports', function (Blueprint $table) {
            $table->integer('sheet')->nullable()->after('papersize'); // Menambahkan kolom 'sheet'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            $table->dropColumn('sheet'); // Menghapus kolom 'sheet'
        });
    }
};
