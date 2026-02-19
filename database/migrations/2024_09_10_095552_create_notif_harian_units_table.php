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
        Schema::create('notif_harian_units', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->json('documentkind'); // To store the list of documentkind IDs
            $table->unsignedBigInteger('telegrammessagesaccount_id')->nullable(); // Unsigned for foreign key
            $table->foreign('telegrammessagesaccount_id')->references('id')->on('telegram_messages_accounts')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notif_harian_units');
    }
};

