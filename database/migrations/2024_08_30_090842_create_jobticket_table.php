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
        Schema::create('jobticket', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jobticket_identity_id');
            $table->string('rev');
            $table->string('status')->nullable();

            $table->string('documentname')->nullable();

            $table->string('level')->nullable();
            $table->unsignedBigInteger('drafter_id')->nullable();
            $table->unsignedBigInteger('checker_id')->nullable();
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->date('deadlinerelease')->nullable();
            $table->timestamps();

            $table->foreign('jobticket_identity_id')->references('id')->on('jobticket_identity')->onDelete('cascade');

            $table->foreign('drafter_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('checker_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobticket');
    }
};
