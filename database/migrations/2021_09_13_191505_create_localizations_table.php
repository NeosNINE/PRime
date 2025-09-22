<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->string('key')->index()->comment('Полный ключ (вместе с разделом)');
            $table->json('text')->comment('Какой текст указан (на разных языках)');
            $table->enum('type',['text','html'])->comment('Как редактируется в админке');
            $table->json('name')->nullable()->comment('Название (подсказка) для админа');
            $table->string('lang_file')->default('content.php')->comment('Файл где будет сохранена локализация');
            $table->unsignedInteger('helper_id')->nullable()->unique();
            $table->unsignedInteger('helper_section_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('localizations');
    }
}
