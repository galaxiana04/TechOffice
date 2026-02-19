<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            $table->unique(['nodokumen', 'newreport_id'], 'unique_nodokumen_newreport');
        });
    }

    public function down(): void
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            $table->dropUnique('unique_nodokumen_newreport');
        });
    }
};
