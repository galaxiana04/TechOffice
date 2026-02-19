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
        Schema::create('memo_sekdiv_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('memo_sekdiv_id');
            $table->string('infostatus'); // contoh: 'documentopened'
            $table->timestamp('entertime'); // waktu aksi
            $table->timestamps();

            $table->foreign('memo_sekdiv_id')
                ->references('id')
                ->on('memosekdivs')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('memo_sekdiv_timelines');
    }
};
