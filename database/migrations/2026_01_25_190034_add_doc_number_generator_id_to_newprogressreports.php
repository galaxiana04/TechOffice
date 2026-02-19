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
        Schema::table('newprogressreports', function (Blueprint $table) {
            $table->foreignId('doc_number_generator_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('newprogressreports', function (Blueprint $table) {
            $table->dropForeign(['doc_number_generator_id']);
            $table->dropColumn('doc_number_generator_id');
        });
    }
};
