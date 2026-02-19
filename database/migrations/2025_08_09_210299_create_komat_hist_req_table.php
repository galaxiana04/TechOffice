<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKomatHistReqTable extends Migration
{
    public function up()
    {
        Schema::create('komat_hist_req', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('komat_process_history_id');
            $table->unsignedBigInteger('komat_requirement_id');
            $table->timestamps();

            $table->foreign('komat_process_history_id')
                ->references('id')
                ->on('komat_process_history')
                ->onDelete('cascade');

            $table->foreign('komat_requirement_id')
                ->references('id')
                ->on('komat_requirement')
                ->onDelete('cascade');

            $table->unique(['komat_process_history_id', 'komat_requirement_id'], 'komat_hist_req_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('komat_hist_req');
    }
}
