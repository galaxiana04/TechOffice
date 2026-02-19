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
        Schema::table('project_types', function (Blueprint $table) {
            $table->string('project_code')->after('title'); // Tambah project_code setelah official_name
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('project_types', function (Blueprint $table) {
            $table->dropColumn('project_code');
        });
    }
};
