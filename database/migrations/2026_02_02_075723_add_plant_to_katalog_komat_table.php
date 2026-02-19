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
        Schema::table('katalog_komat', function (Blueprint $table) {
            $table->string('plant')->nullable()->after('UoM'); // setelah kolom UoM
        });
    }

    public function down(): void
    {
        Schema::table('katalog_komat', function (Blueprint $table) {
            $table->dropColumn('plant');
        });
    }
};
