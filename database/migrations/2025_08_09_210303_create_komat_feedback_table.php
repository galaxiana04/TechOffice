<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKomatFeedbackTable extends Migration
{
    public function up()
    {
        Schema::create('komat_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('komat_process_history_id');
            $table->unsignedBigInteger('komat_requirement_id');
            $table->unsignedBigInteger('komat_position_id');
            $table->text('comment')->nullable();
            $table->enum('status', ['last_accepted', 'draft', 'reviewed'])->default('draft');
            $table->enum('feedback_status', ['draft', 'approved', 'notapproved', 'withremarks'])->default('draft');
            $table->string('user_rule')->nullable();
            $table->string('user_name')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('komat_position_id')
                ->references('id')
                ->on('komat_position')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('komat_process_history_id')
                ->references('id')
                ->on('komat_process_history')
                ->onDelete('cascade');

            $table->foreign('komat_requirement_id')
                ->references('id')
                ->on('komat_requirement')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('komat_feedback');
    }
}
