<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ai_customs', function (Blueprint $table) {
            $table->unsignedBigInteger('aicustomspeciality_id')->nullable()->after('description');
            $table->foreign('aicustomspeciality_id')->references('id')->on('ai_custom_specialities')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('ai_customs', function (Blueprint $table) {
            $table->dropForeign(['aicustomspeciality_id']);
            $table->dropColumn('aicustomspeciality_id');
        });
    }
};
