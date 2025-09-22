<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promo_code_user', function (Blueprint $table) {
            if (!Schema::hasColumn('promo_code_user', 'used_at')) {
                $table->timestamp('used_at')->nullable()->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('promo_code_user', function (Blueprint $table) {
            if (Schema::hasColumn('promo_code_user', 'used_at')) {
                $table->dropColumn('used_at');
            }
        });
    }
};


