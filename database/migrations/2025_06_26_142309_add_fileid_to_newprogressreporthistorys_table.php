<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newprogressreporthistorys', function (Blueprint $table) {
            $table->string('fileid')->nullable()->after('rev');
        });
    }

    public function down(): void
    {
        Schema::table('newprogressreporthistorys', function (Blueprint $table) {
            $table->dropColumn('fileid');
        });
    }
};
