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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('count')->default(0);
            $table->string('documentname');
            $table->string('filename');
            $table->string('author');
            $table->text('metadata');
            $table->text('linkfile');
            $table->text('comment')->nullable();
            $table->string('category')->nullable();
            $table->string('project_type')->nullable();
            $table->string('project_pic')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
