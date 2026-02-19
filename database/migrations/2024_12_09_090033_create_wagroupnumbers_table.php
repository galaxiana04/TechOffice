<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWagroupnumbersTable extends Migration
{
    public function up()
    {
        Schema::create('wagroupnumbers', function (Blueprint $table) {
            $table->id();
            $table->string('groupname');
            $table->string('number');
            $table->boolean('isverified')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wagroupnumbers');
    }
}
