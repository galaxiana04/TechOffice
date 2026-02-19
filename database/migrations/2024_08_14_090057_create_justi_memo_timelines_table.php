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
        Schema::create('justi_memo_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('justi_memo_id')->constrained()->onDelete('cascade');
            $table->string('infostatus');
            $table->timestamp('entertime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('justi_memo_timelines');
    }
};
