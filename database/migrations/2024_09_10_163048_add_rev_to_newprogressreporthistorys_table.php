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
        Schema::table('newprogressreporthistorys', function (Blueprint $table) {
            $table->string('dcr')->nullable()->after('status'); // Add the 'rev' column after the 'status' column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newprogressreporthistorys', function (Blueprint $table) {
            $table->dropColumn('dcr'); // Drop the 'rev' column when rolling back the migration
        });
    }
};
