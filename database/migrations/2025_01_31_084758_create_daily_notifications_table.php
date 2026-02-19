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
        Schema::create('daily_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('read_status', ['read', 'unread'])->default('unread'); // Menambahkan status read/unread
            $table->timestamp('day'); // Default ke timestamp saat ini
            $table->unsignedBigInteger('notif_harian_unit_id')->nullable(); // Menambahkan kolom idunit yang boleh NULL
            $table->timestamps();

            // Menambahkan foreign key constraint ke tabel notif_harian_units
            $table->foreign('notif_harian_unit_id')->references('id')->on('notif_harian_units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('daily_notifications');
    }
};
