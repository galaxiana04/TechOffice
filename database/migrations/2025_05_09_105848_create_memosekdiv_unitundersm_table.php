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
        Schema::create('memo_sekdiv_unit_under_sms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('memo_sekdiv_sm_decision_id');
            $table->string('unitname'); // Kolom unitname wajib diisi
            $table->timestamps();

            $table->foreign('memo_sekdiv_sm_decision_id')
                ->references('id')
                ->on('memo_sekdiv_sm_decisions')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('memo_sekdiv_unit_under_sms');
    }
};
