<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('notulens', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('place');

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('agenda_notulen_id')->constrained()->onDelete('cascade');

            $table->timestamp('notulen_time_start');
            $table->timestamp('notulen_time_end');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();

            $table->unique(['number', 'agenda_notulen_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notulens');
    }
};
