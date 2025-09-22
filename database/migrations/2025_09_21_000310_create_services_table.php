<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('min_quantity')->default(1);
            $table->unsignedInteger('max_quantity')->default(1);
            $table->decimal('cost_price', 12, 4)->default(0);
            $table->decimal('price', 12, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_manual')->default(false);
            $table->unsignedBigInteger('total_orders')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['provider_id', 'external_id']);
            $table->index('service_category_id');
            $table->index('provider_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
