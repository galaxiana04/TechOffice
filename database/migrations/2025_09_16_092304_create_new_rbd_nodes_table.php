<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_rbd_nodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rbd_instance_id');
            $table->unsignedBigInteger('failure_rate_id')->nullable();
            $table->string('key_value', 50)->index();
            $table->enum('category', ['start', 'end', 'junction', 'component']);


            $table->enum('configuration', ['single', 'series', 'parallel', 'k-out-of-n'])->default('single');
            $table->integer('quantity')->default(1);


            $table->string('code', 100)->nullable();
            $table->string('name', 255)->nullable();
            $table->integer('x')->nullable();
            $table->integer('y')->nullable();
            $table->decimal('reliability', 20, 18)->nullable();
            $table->integer('k')->nullable();
            $table->integer('n')->nullable();
            $table->foreign('rbd_instance_id')
                ->references('id')
                ->on('new_rbd_instances')
                ->onDelete('cascade');
            $table->foreign('failure_rate_id')
                ->references('id')
                ->on('new_rbd_failure_rates')
                ->onDelete('set null');
            $table->unique(['rbd_instance_id', 'key_value']);
            $table->unsignedBigInteger('foreign_instance_id')->nullable()->after('failure_rate_id');
            $table->foreign('foreign_instance_id')->references('id')->on('new_rbd_instances')->onDelete('set null');
            $table->decimal('t_initial', 12, 4)
                ->default(0.0000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_rbd_nodes');
    }
};
