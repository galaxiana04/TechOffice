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
        Schema::dropIfExists('zoom_access_tokens');
        Schema::create('zoom_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('account_name');
            $table->string('zoom_clientid')->nullable();
            $table->string('zoom_clientsecret')->nullable();
            $table->string('zoom_redirecturl')->nullable();
            $table->string('zoom_hotkey')->nullable();
            $table->string('jenis')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('account_expired')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_access_tokens');
    }
};
