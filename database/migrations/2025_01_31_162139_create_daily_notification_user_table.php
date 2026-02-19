<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('daily_notification_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daily_notification_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('read_status', ['read', 'unread'])->default('unread');
            $table->timestamps();

            // Foreign keys
            $table->foreign('daily_notification_id')->references('id')->on('daily_notifications')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Supaya tidak ada duplikasi
            $table->unique(['daily_notification_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('daily_notification_user');
    }
};
