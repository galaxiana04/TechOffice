<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('topic_notulens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notulen_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->timestamps();
            $table->enum('status', ['open', 'closed'])->default('open');

            $table->unique(['notulen_id', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_notulens');
    }
};
