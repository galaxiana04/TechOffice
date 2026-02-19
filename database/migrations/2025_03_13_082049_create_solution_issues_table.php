<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solution_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_notulen_id')->constrained()->onDelete('cascade');
            $table->text('followup');
            $table->string('pic');
            $table->text('update')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->date('deadlinedate')->nullable();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solution_issues');
    }
};
