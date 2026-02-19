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
        Schema::table('newprogressreporthistorys', function (Blueprint $table) {
            $table->integer('sheet')->nullable()->after('dcr'); // Ganti 'existing_column' dengan nama kolom yang sesuai
            $table->string('papersize')->nullable()->after('sheet');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newprogressreporthistorys', function (Blueprint $table) {
            $table->dropColumn(['sheet', 'papersize']);
        });

    }
};
