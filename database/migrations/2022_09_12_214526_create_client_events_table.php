<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('client_events', function (Blueprint $table) {
            $table->id();

            $table->string('event_name')->index();
            $table->json('data')->nullable();
            $table->string('access_key')->nullable()->comment('Какой доступ должна иметь роль для этого события');
            $table->boolean('unique')->default(false)->comment('Если TRUE, то на клиенте только один раз будет выполняться за один AJAX');

            $table->unsignedBigInteger('created_user_id')->nullable()->comment('User который запустил событие');
            $table->foreign('created_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('for_user_id')->nullable()->comment('User которому предназначено событие');
            $table->foreign('for_user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamp('created_at')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('client_events');
    }
};
