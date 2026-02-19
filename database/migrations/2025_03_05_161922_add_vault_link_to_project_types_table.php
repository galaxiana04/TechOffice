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
        Schema::table('project_types', function (Blueprint $table) {
            $table->string('vault_link')->nullable()->after('title'); // Menambahkan kolom vault_link setelah title
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_types', function (Blueprint $table) {
            $table->dropColumn('vault_link'); // Menghapus kolom vault_link saat rollback
        });
    }
};
