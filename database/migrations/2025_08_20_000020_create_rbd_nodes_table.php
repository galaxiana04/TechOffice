<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rbd_nodes', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['series', 'parallel', 'block', 'k-out-of-n']);
            $table->enum('block_group_type', ['single', 'series', 'parallel', 'k-out-of-n'])->nullable();
            $table->unsignedInteger('block_count')->nullable();
            $table->unsignedInteger('k_value')->nullable(); // Kolom untuk menyimpan k
            $table->float('x')->default(0); // X-coordinate for node position 
            $table->float('y')->default(0); // Y-coordinate for node position 
            $table->string('code')->unique(); // Unique node identifier
            // relasi ke rbd_blocks
            $table->foreignId('rbd_block_id')
                ->nullable()
                ->constrained('rbd_blocks')
                ->nullOnDelete();

            // self-reference untuk struktur tree
            $table->unsignedBigInteger('parent_id')->nullable();
            // relasi ke rbdidentity
            $table->unsignedBigInteger('rbdidentity_id')->nullable();
            $table->foreign('rbdidentity_id')
                ->references('id')
                ->on('rbdidentity')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rbd_nodes');
    }
};
