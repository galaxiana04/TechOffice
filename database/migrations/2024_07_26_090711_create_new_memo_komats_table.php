<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('new_memo_komats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('new_memo_id')->constrained()->onDelete('cascade');
            $table->string('kodematerial');
            $table->string('material');
            $table->string('supplier');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('new_memo_komats');
    }
};
