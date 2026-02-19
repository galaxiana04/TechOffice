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
        Schema::table('newboms', function (Blueprint $table) {
            $table->unsignedBigInteger('proyek_type_id')->nullable()->after('unit');

            // Menambahkan foreign key constraint
            $table->foreign('proyek_type_id')->references('id')->on('project_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newboms', function (Blueprint $table) {
            $table->dropForeign(['proyek_type_id']);
            $table->dropColumn('proyek_type_id');
        });
    }
};
