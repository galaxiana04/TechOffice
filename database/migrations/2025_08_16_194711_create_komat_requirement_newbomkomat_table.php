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
        Schema::create('komat_requirement_newbomkomat', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('newbomkomat_id');
            $table->unsignedBigInteger('komat_requirement_id');

            $table->timestamps();

            // Foreign keys
            $table->foreign('newbomkomat_id')
                ->references('id')
                ->on('newbomkomats')
                ->onDelete('cascade');

            $table->foreign('komat_requirement_id')
                ->references('id')
                ->on('komat_requirement')
                ->onDelete('cascade');

            // Unique supaya tidak duplikat
            $table->unique(['newbomkomat_id', 'komat_requirement_id'], 'unique_newbomkomat_requirement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komat_requirement_newbomkomat');
    }
};
