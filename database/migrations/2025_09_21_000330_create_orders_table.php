<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_id')->nullable();
            $table->text('link');
            $table->unsignedInteger('quantity');
            $table->decimal('price', 12, 4);
            $table->decimal('cost_price', 12, 4)->default(0);
            $table->string('status')->default('pending');
            $table->boolean('is_drip_feed')->default(false);
            $table->unsignedInteger('drip_runs')->nullable();
            $table->unsignedInteger('drip_interval_minutes')->nullable();
            $table->unsignedInteger('drip_runs_processed')->default(0);
            $table->boolean('is_manual')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refunded_amount', 12, 4)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
