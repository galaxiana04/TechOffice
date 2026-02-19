<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newprogressreport_documentkind', function (Blueprint $table) {
            $table->foreignId('unit_id')
                ->nullable()                                   // biarkan NULL utk data lama
                ->constrained('newprogressreport_units')       // ⬅️ arahkan ke tabel yg benar
                ->nullOnDelete()                               // jika unit dihapus, set NULL
                ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('newprogressreport_documentkind', function (Blueprint $table) {
            // Laravel 9/10: ini akan meng‑drop FK & kolom sekaligus
            $table->dropConstrainedForeignId('unit_id');
        });
    }
};
