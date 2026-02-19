<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('newboms', function (Blueprint $table) {
            // Drop the 'proyek_type' column
            $table->dropColumn('proyek_type');

            // Drop unique constraint from 'BOMnumber'
            $table->dropUnique('newboms_bomnumber_unique'); // Name format: {table}_{column}_unique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('newboms', function (Blueprint $table) {
            // Recreate the 'proyek_type' column
            $table->string('proyek_type');

            // Reapply the unique constraint on 'BOMnumber'
            $table->string('BOMnumber')->unique()->change();
        });
    }
};
