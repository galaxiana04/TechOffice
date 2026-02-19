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
            $table->enum('configurationrule', ['series', 'parallel'])
                ->default('parallel')
                ->after('is_expand_to_logistic');
        });
    }

    public function down(): void
    {
        Schema::table('new_memos', function (Blueprint $table) {
            $table->dropColumn('configurationrule');
        });
    }
};
