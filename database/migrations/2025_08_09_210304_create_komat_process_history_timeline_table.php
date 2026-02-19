<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKomatProcessHistoryTimelineTable extends Migration
{
    public function up()
    {
        Schema::create('komat_process_history_timeline', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('komat_process_history_id');
            $table->string('infostatus')->nullable();
            $table->timestamp('entertime')->useCurrent();
            $table->timestamps();

            $table->foreign('komat_process_history_id')
                ->references('id')
                ->on('komat_process_history')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('komat_process_history_timeline');
    }
}
