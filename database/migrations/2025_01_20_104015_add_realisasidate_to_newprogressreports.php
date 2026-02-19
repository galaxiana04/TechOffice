<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            $table->timestamp('realisasidate')->nullable()->after('realisasi');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            $table->dropColumn('realisasidate');
        });
    }
};
