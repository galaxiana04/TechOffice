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
        Schema::create('justi_memo_feedback', function (Blueprint $table) {
            $table->id();  // Primary key, auto-incremented
            $table->unsignedBigInteger('justi_memo_id');  // Foreign key ke justi_memos
            $table->unsignedBigInteger('pic');  // Foreign key ke users (terhubung dengan user ID)
            $table->string('level')->nullable();
            $table->string('hasilreview')->nullable();
            $table->string('sudahdibaca')->nullable();
            $table->text('comment')->nullable();
            $table->string('conditionoffile')->nullable();
            $table->string('conditionoffile2')->nullable();
            $table->timestamps();  // Kolom created_at dan updated_at otomatis

            // Add foreign key constraint to justi_memos
            $table->foreign('justi_memo_id')
                  ->references('id')
                  ->on('justi_memos')
                  ->onDelete('cascade');  // Cascade delete

            // Add foreign key constraint to users
            $table->foreign('pic')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');  // Cascade delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('justi_memo_feedback');
    }
};
