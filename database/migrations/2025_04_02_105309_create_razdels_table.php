<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('razdels', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->unsignedSmallInteger('parent_id')->default(0)->comment('Родительский ID');
            $table->unsignedSmallInteger('lvl')->default(1)->comment('Уровень');
            $table->unsignedTinyInteger('npp')->default(1)->comment('Номер по порядку');
            $table->string('title', 255)->comment('Наименование страницы');
            $table->string('description', 255)->nullable()->comment('Описание');
            $table->string('keywords', 255)->nullable()->comment('Ключевые слова');
            $table->string('url', 64)->unique()->comment('Адрес страницы');
            $table->tinyInteger('feedback')->default(0)->comment('Наличие формы обратной связи');
            $table->string('feedback_title', 64)->nullable()->comment('Заголовок в  форме обратной связи');
            $table->comment('Разделы сайта');
            $table->timestamps();
            $table->primary('id');
            $table->fullText('url');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('razdels');
    }
};
