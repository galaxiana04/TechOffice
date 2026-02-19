<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('new_rbd_instances', function (Blueprint $table) {
            $table->foreignId('new_rbd_model_id')
                ->constrained('new_rbd_models')
                ->cascadeOnDelete(); // <-- INI YANG BENAR!
        });
    }

    public function down(): void
    {
        Schema::table('new_rbd_instances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('new_rbd_model_id');
        });
    }
};
