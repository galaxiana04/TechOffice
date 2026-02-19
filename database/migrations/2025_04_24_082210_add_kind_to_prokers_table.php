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
        Schema::table('prokers', function (Blueprint $table) {
            $table->enum('kind', ['proker', 'lpk'])
                  ->default('proker')
                  ->after('proker_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prokers', function (Blueprint $table) {
            $table->dropColumn('kind');
        });
    }
};
