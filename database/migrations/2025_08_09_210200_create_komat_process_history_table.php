<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKomatProcessHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('komat_process_history', function (Blueprint $table) {
            $table->id();
            $table->string('no_prefix', 10)->nullable();
            $table->integer('no_counter')->nullable();
            $table->string('no_midcode', 10)->nullable();
            $table->year('no_year')->nullable();
            $table->string('no_dokumen', 30)->nullable();


            $table->unsignedBigInteger('komat_process_id');
            $table->unsignedTinyInteger('discussion_number')->default(1);
            $table->unsignedBigInteger('project_type_id')->nullable(); // Added 
            $table->unsignedBigInteger('unit_distributor_id');  // revisi nama kolom
            $table->unsignedBigInteger('komat_supplier_id');   // kolom baru relasi ke supplier
            $table->unsignedBigInteger('komat_requirement_id')->nullable(); // ✅ relasi ke requirement
            $table->enum('status', ['Terbuka', 'Tertutup'])->default('Terbuka');
            $table->enum('documentstatus', ['ongoing', 'approved', 'rejectedbysm', 'rejectedbylogistik'])->default('ongoing');
            $table->enum('logisticauthoritylevel', ['verifiednotneeded', 'managerneeded', 'seniormanagerneeded'])->default('verifiednotneeded');
            $table->string('revision')->nullable();
            $table->text('note')->nullable(); // Added note column
            $table->text('rejectedreason')->nullable(); // Added rejected reason column
            $table->timestamps();

            $table->foreign('komat_process_id')
                ->references('id')
                ->on('komat_process')
                ->onDelete('cascade');

            $table->foreign('unit_distributor_id')
                ->references('id')
                ->on('units')
                ->onDelete('cascade');
            // Relasi ke komat_supplier
            $table->foreign('komat_supplier_id')
                ->references('id')
                ->on('komat_supplier')
                ->onDelete('cascade');

            $table->foreign('project_type_id')
                ->references('id')
                ->on('project_types')
                ->onDelete('set null');

            // ✅ Relasi ke komat_requirement
            $table->foreign('komat_requirement_id')
                ->references('id')
                ->on('komat_requirement')
                ->onDelete('set null');

            // Tambahkan unique key gabungan
            $table->unique(['no_midcode', 'no_year', 'no_counter'], 'unique_midcode_year_counter');
        });
    }

    public function down()
    {
        Schema::dropIfExists('komat_process_history');
    }
}
