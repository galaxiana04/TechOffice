<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('memosekdiv_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('memo_sekdiv_id'); // Kolom relasi ke MemoSekdiv

            $table->string('pic');
            $table->string('author');
            $table->string('level');
            $table->string('email');
            $table->string('reviewresult')->nullable();
            $table->string('condition1')->nullable();
            $table->string('condition2')->nullable();
            $table->boolean('isread')->default(false);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('memo_sekdiv_id')->references('id')->on('memosekdivs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memosekdiv_feedbacks');
    }
};
