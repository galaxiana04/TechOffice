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
        Schema::table('new_memos', function (Blueprint $table) {
            $table->boolean('is_expand_to_logistic')->default(false)->after('proyek_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('new_memos', function (Blueprint $table) {
            $table->dropColumn('is_expand_to_logistic');
        });
    }
};
