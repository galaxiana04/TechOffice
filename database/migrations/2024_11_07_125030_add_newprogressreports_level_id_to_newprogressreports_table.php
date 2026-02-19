<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('newprogressreport_documentkind', function (Blueprint $table) {
            $table->unsignedBigInteger('newprogressreports_level_id')->nullable();
            $table->foreign('newprogressreports_level_id', 'fk_dockind_level_id')
                ->references('id')
                ->on('new_progress_reports_levels')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('newprogressreport_documentkind', function (Blueprint $table) {
            $table->dropForeign('fk_dockind_level_id');
            $table->dropColumn('newprogressreports_level_id');
        });
    }
};
