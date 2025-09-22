<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('driver');
            $table->string('api_url');
            $table->text('api_key');
            $table->boolean('is_active')->default(true);
            $table->decimal('balance', 16, 4)->default(0);
            $table->string('currency', 8)->default('USD');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('services_last_synced_at')->nullable();
            $table->decimal('low_balance_threshold', 16, 4)->nullable();
            $table->timestamp('last_balance_notification_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('driver');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
