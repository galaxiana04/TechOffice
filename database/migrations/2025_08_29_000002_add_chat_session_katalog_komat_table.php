<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_session_katalog_komat', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages_katalog_komat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained('chat_session_katalog_komat')->onDelete('cascade');
            $table->enum('sender', ['user', 'bot']);
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages_katalog_komat');
        Schema::dropIfExists('chat_session_katalog_komat');
    }
};
