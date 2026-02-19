<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('new_rbd_links', function (Blueprint $table) {
            $table->unsignedBigInteger('from_node_id')->nullable()->after('rbd_instance_id');
            $table->unsignedBigInteger('to_node_id')->nullable()->after('from_node_id');

            $table->foreign('from_node_id')
                ->references('id')
                ->on('new_rbd_nodes')
                ->onDelete('cascade');

            $table->foreign('to_node_id')
                ->references('id')
                ->on('new_rbd_nodes')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('new_rbd_links', function (Blueprint $table) {
            $table->dropForeign(['from_node_id']);
            $table->dropForeign(['to_node_id']);
            $table->dropColumn(['from_node_id', 'to_node_id']);
        });
    }
};
