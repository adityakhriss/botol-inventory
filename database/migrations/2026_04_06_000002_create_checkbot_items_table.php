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
        Schema::create('checkbot_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checkbot_run_id')->constrained('checkbot_runs')->cascadeOnDelete();
            $table->foreignId('bottle_id')->constrained('bottles')->restrictOnDelete();
            $table->string('bottle_code_snapshot');
            $table->text('parameter')->nullable();
            $table->text('test_result')->nullable();
            $table->enum('status', ['LULUS', 'BELUM_LULUS'])->nullable();
            $table->timestamps();

            $table->unique(['checkbot_run_id', 'bottle_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkbot_items');
    }
};
