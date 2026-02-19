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
        Schema::table('system_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('user');

            // Optional: Add a foreign key constraint if user_id references an id in the users table
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('system_logs', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
