<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('new_rbd_nodes', function (Blueprint $table) {
            $table->unsignedBigInteger('foreign_instance_id')->nullable()->after('failure_rate_id');
            $table->foreign('foreign_instance_id')->references('id')->on('new_rbd_instances')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('new_rbd_nodes', function (Blueprint $table) {
            $table->dropForeign(['foreign_instance_id']);
            $table->dropColumn('foreign_instance_id');
        });
    }
};
