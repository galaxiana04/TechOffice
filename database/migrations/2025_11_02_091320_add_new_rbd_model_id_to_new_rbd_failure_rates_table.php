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
        Schema::table('new_rbd_failure_rates', function (Blueprint $table) {
            $table->foreignId('new_rbd_model_id')
                ->after('source') // letakkan setelah kolom source (opsional)
                ->constrained('new_rbd_models')
                ->cascadeOnDelete(); // ikut terhapus jika model dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_rbd_failure_rates', function (Blueprint $table) {
            $table->dropForeign(['new_rbd_model_id']);
            $table->dropColumn('new_rbd_model_id');
        });
    }
};
