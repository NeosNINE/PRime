<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('external_id')->nullable();
            $table->decimal('amount_usd', 12, 2);
            $table->string('method')->nullable();
            $table->enum('status', ['pending','completed','failed'])->default('completed');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id','created_at']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};


