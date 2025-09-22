<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('path')->unique();
            $table->string('original_name');
            $table->string('model')->nullable();
            $table->bigInteger('model_id')->nullable();
            $table->string('field_key')->nullable();
            $table->boolean('used')->default(false)->comment('Использован ли файл где-то')->index();

            $table->timestamps();

            $table->index(['model', 'model_id']);

        });
    }

    public function down()
    {
        Schema::dropIfExists('file_uploads');
    }
};
