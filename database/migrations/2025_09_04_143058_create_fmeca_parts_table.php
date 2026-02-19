<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fmeca_parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fmeca_identity_id'); // relasi ke fmeca_identities
            $table->string('name'); // nama part / subsystem / komponen
            $table->timestamps();

            $table->foreign('fmeca_identity_id')
                ->references('id')
                ->on('fmeca_identities')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fmeca_parts');
    }
};
