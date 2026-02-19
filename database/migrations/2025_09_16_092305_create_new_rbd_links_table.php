<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_rbd_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rbd_instance_id');
            $table->string('from_key', 50);
            $table->string('to_key', 50);
            $table->foreign('rbd_instance_id')
                ->references('id')
                ->on('new_rbd_instances')
                ->onDelete('cascade');
            $table->foreign('from_key')
                ->references('key_value')
                ->on('new_rbd_nodes')
                ->onDelete('cascade');
            $table->foreign('to_key')
                ->references('key_value')
                ->on('new_rbd_nodes')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_rbd_links');
    }
};
