<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fmecas', function (Blueprint $table) {
            $table->foreignId('fmeca_identity_id')
                ->nullable()
                ->after('project_type_id') // posisinya bisa disesuaikan
                ->constrained()
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('fmecas', function (Blueprint $table) {
            $table->dropForeign(['fmeca_identity_id']);
            $table->dropColumn('fmeca_identity_id');
        });
    }
};
