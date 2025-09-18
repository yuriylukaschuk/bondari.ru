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
        Schema::create('blocks', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->unsignedSmallInteger('razdel_id')->comment('Раздел (страница) сайта');
            $table->unsignedTinyInteger('npp')->default(1)->comment('Номер блока');
            $table->unsignedTinyInteger('images')->default(0)->comment('Количество фотографий в блоке');
            $table->unsignedTinyInteger('imageset')->default(0)->comment('Блок является рядом фотографий');
            $table->unsignedTinyInteger('positions')->default(0)->comment('Количество компонент в блоке');
            $table->string('class', 255)->nullable()->comment('CSS класс');
            $table->string('element', 255)->nullable()->comment('Тег окончания блока');
            $table->string('class_button', 255)->nullable()->comment('Тег окончания блока');
            $table->comment('Блоки на странице сайта');
            $table->timestamps();
            $table->primary('id');
            $table->foreign('razdel_id')
                ->references('id')
                ->on('razdels')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
