<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('daily_notification_new_progress_report', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daily_notification_id');
            $table->unsignedBigInteger('new_progress_report_history_id');

            // Definisikan Foreign Key dengan nama yang lebih pendek
            $table->foreign('daily_notification_id', 'dn_npr_dn_fk')
                ->references('id')->on('daily_notifications')
                ->onDelete('cascade');

            $table->foreign('new_progress_report_history_id', 'dn_npr_nprh_fk')
                ->references('id')->on('newprogressreporthistorys')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_notification_new_progress_report');
    }
};
