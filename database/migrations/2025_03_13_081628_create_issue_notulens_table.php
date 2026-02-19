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
        Schema::create('issue_notulens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_notulen_id')->constrained()->onDelete('cascade');
            $table->text('issue');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_notulens');
    }
};
