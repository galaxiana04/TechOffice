<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('prokers', function (Blueprint $table) {
            $table->boolean('ishide')->default(false)->after('proker_created_at');
            $table->boolean('ispercentageflexible')->default(true)->after('ishide');
        });
    }

    public function down()
    {
        Schema::table('prokers', function (Blueprint $table) {
            $table->dropColumn('ishide');
            $table->dropColumn('ispercentageflexible');
        });
    }
};
