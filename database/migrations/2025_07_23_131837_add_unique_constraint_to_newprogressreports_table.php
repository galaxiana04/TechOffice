<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToNewprogressreportsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            $table->unique(['newreport_id', 'nodokumen'], 'unique_newreport_nodokumen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            $table->dropUnique('unique_newreport_nodokumen');
        });
    }
}
