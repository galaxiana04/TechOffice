<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('memosekdivs', function (Blueprint $table) {
            $table->id();
            $table->string('documentname');
            $table->string('documentnumber')->unique();
            $table->unsignedBigInteger('project_type_id')->nullable();
            $table->enum('documentstatus', ['open', 'close'])->default('open');
            $table->enum('documentkind', ['Memo', 'Surat Dinas'])->default('Surat Dinas');
            $table->timestamps();

            $table->foreign('project_type_id')->references('id')->on('project_types')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memosekdivs');
    }
};
