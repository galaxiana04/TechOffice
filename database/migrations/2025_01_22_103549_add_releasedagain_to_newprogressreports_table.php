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
            $table->boolean('releasedagain')->default(0)->after('realisasi'); // Menambahkan kolom releasedagain
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            $table->dropColumn('releasedagain'); // Menghapus kolom releasedagain
        });
    }
};
