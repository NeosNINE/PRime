<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('run_number');
            $table->unsignedInteger('quantity');
            $table->string('status')->default('pending');
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'run_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_runs');
    }
};
