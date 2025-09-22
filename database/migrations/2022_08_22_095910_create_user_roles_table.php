<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {

        //Роли
        Schema::create('roles', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('key')->unique()->comment('Уникальный ключ роли.');
            $table->json('access')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        //Связь ролей и юзеров
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->nullable()->constrained()->onDelete('cascade');

        });

    }

    public function down()
    {

        Schema::table('role_user', function (Blueprint $table) {

            $table->dropForeign('role_user_user_id_foreign');
            $table->dropForeign('role_user_role_id_foreign');

        });

        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_user');
    }
};
