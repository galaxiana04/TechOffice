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
            // Drop the old 'deadlinerelease' column
            $table->dropColumn('deadlinerelease');

            // Add the new 'deadlinereleasedate' and 'startreleasedate' columns with timestamp type
            $table->timestamp('deadlinereleasedate')->nullable();
            $table->timestamp('startreleasedate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newprogressreporthistorys', function (Blueprint $table) {
            // Restore the old 'deadlinerelease' column
            $table->string('deadlinerelease')->nullable();

            // Drop the new 'deadlinereleasedate' and 'startreleasedate' columns
            $table->dropColumn('deadlinereleasedate');
            $table->dropColumn('startreleasedate');
        });
    }
};
