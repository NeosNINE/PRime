<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( !Schema::hasTable('emails') ){

            Schema::create('emails', function (Blueprint $table) {
                $table->id();
                $table->string('email')->nullable()->comment('На какой Email отправлять');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('subject')->nullable()->comment('Тема');
                $table->mediumText('text')->nullable()->comment('Сообщение (html)');
                $table->mediumText('text_plain')->nullable()->comment('Сообщение (text_plain)');
                $table->string('type')->default('other')->comment('Тип')->index();
                $table->enum('status', [ 'sending', 'success', 'error'])->default('sending')->comment('Статус')->index();
                $table->string('key')->nullable()->comment('Ключ')->index();
                $table->timestamp('sent_date')->nullable()->comment('Время отправки');
                $table->json('data')->nullable()->comment('Данные');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emails');
    }
}
