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
        Schema::create('newprogressreport_subtack_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subtack_member_id')->constrained('subtack_members')->onDelete('cascade');
            $table->foreignId('newprogressreport_id')->constrained('newprogressreports')->onDelete('cascade');
            $table->timestamps();

            // Memberikan nama indeks unik yang lebih pendek
            $table->unique(['subtack_member_id', 'newprogressreport_id'], 'uniq_subtack_newprogress');
        });
    }

    public function down()
    {
        Schema::dropIfExists('newprogressreport_subtack_member');
    }
};
