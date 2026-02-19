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
        Schema::table('rams_documents', function (Blueprint $table) {
            $table->enum('status', ['Terbuka', 'Tertutup'])->default('Terbuka')->after('ramsdocument_unit');
        });
    }

    public function down(): void
    {
        Schema::table('rams_documents', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
