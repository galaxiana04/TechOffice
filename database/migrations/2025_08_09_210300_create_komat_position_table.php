<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKomatPositionTable extends Migration
{
    public function up()
    {
        Schema::create('komat_position', function (Blueprint $table) {
            $table->id();
            // Kolom mengacu ke komat_hist_req id
            $table->unsignedBigInteger('komat_hist_req_id');
            $table->unsignedBigInteger('unit_id');

            $table->enum('level', [
                'logistik_upload',
                'prediscussion',
                'discussion',
                'resume',
                'sm_level',
                'mtpr_review',
                'logistik_done',
                'managerlogistikneeded',
                'seniormanagerlogistikneeded'
            ])->default('logistik_upload');

            $table->enum('status', ['draft', 'approved', 'notapproved', 'withremarks', 'notstarted'])->default('draft');

            $table->enum('status_process', ['not_started', 'ongoing', 'done'])->default('not_started');

            $table->timestamps();

            // Foreign keys
            $table->foreign('komat_hist_req_id')
                ->references('id')
                ->on('komat_hist_req')
                ->onDelete('cascade');

            $table->foreign('unit_id')
                ->references('id')
                ->on('units')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('komat_position');
    }
}
