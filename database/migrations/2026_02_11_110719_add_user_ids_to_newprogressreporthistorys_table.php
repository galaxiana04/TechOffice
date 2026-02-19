<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('newprogressreporthistorys', function (Blueprint $table) {
            $table->unsignedBigInteger('drafter_id')->nullable()->after('drafter');
            $table->unsignedBigInteger('checker_id')->nullable()->after('checker');

            // optional: add foreign key constraint
            $table->foreign('drafter_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('checker_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('newprogressreporthistorys', function (Blueprint $table) {
            $table->dropForeign(['drafter_id']);
            $table->dropForeign(['checker_id']);
            $table->dropColumn(['drafter_id', 'checker_id']);
        });
    }
};
