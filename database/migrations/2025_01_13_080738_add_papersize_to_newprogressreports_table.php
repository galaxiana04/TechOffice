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
            $table->string('papersize')->nullable()->after('temporystatus'); // Kolom baru ditambahkan
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            $table->dropColumn('papersize');
        });

    }
};
