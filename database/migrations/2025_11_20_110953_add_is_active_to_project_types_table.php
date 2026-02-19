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
        Schema::table('project_types', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('vault_link');
            // Jika ingin ada kolom untuk keterangan (opsional)
            // $table->text('notes')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_types', function (Blueprint $table) {
            $table->dropColumn('is_active');
            // Jika menambahkan notes di atas, hapus juga:
            // $table->dropColumn('notes');
        });
    }
};
