<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('meeting_room_id')->nullable()->after('room');
            $table->foreign('meeting_room_id')->references('id')->on('meeting_rooms')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['meeting_room_id']);
            $table->dropColumn('meeting_room_id');
        });
    }
};
