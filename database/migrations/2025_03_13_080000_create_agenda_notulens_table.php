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
        Schema::create('agenda_notulens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('project_type_id');
            $table->timestamps();
            $table->unique(['name', 'project_type_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('agenda_notulens');
    }

};
