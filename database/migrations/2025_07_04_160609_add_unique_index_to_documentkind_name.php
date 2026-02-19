<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah constraint UNIQUE ke kolom name.
     */
    public function up(): void
    {
        Schema::table('newprogressreport_documentkind', function (Blueprint $table) {
            // Cara paling aman: tambahkan indeks UNIQUE terpisah
            // Nama indeks otomatis: newprogressreport_documentkind_name_unique
            $table->unique('name');
        });
    }

    /**
     * Rollback constraint UNIQUE.
     */
    public function down(): void
    {
        Schema::table('newprogressreport_documentkind', function (Blueprint $table) {
            $table->dropUnique(['name']); // atau $table->dropUnique('newprogressreport_documentkind_name_unique');
        });
    }
};
