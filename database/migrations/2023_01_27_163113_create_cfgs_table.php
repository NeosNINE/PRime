<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cfg', function (Blueprint $table) {
            $table->id();

            $table->string('key')->unique();
            $table->mediumText('value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cfg');
    }
};
