<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewMemoFeedbackTable extends Migration
{
    public function up()
    {
        Schema::create('new_memo_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('new_memo_id');
            $table->string('pic');
            $table->string('author');
            $table->string('level')->nullable();
            $table->string('email')->nullable();

            $table->string('hasilreview')->nullable();
            $table->string('sudahdibaca')->nullable();
            
            $table->text('comment')->nullable();
            $table->string('conditionoffile')->nullable();
            $table->string('conditionoffile2')->nullable();
            $table->timestamps();

            // Add foreign key constraints if necessary
            $table->foreign('new_memo_id')->references('id')->on('new_memos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('new_memo_feedback');
    }
}
