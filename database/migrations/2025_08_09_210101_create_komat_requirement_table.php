<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKomatRequirementTable extends Migration
{
    public function up()
    {
        Schema::create('komat_requirement', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();  // kolom description, boleh kosong
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('komat_requirement');
    }
}
