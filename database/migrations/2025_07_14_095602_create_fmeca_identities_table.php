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
        Schema::create('fmeca_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_type_id')->constrained()->onDelete('cascade')->comment('Foreign key to project_types table');
            // Tambahkan kolom lain di bawah ini jika dibutuhkan
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fmeca_identities');
    }
};
