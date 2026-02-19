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
        Schema::create('rams_document_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rams_document_id')->constrained('rams_documents')->onDelete('cascade');
            $table->string('pic')->nullable();
            $table->string('author');
            $table->string('level')->nullable();
            $table->string('email')->nullable();
            $table->text('comment');
            $table->string('conditionoffile')->nullable();
            $table->string('conditionoffile2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rams_document_feedbacks');
    }
};
