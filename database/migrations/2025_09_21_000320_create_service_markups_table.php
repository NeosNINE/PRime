<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_markups', function (Blueprint $table) {
            $table->id();
            $table->enum('scope', ['global', 'provider', 'category', 'service']);
            $table->foreignId('provider_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('percent', 8, 4)->nullable();
            $table->decimal('fixed', 12, 4)->nullable();
            $table->timestamps();

            $table->unique(['scope', 'provider_id', 'service_category_id', 'service_id'], 'service_markups_unique_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_markups');
    }
};
