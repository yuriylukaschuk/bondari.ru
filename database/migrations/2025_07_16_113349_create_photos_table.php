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
        Schema::create('photos', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->string('url', 255)->nullable()->comment('Файл изображения');
            $table->string('thumbnail', 255)->nullable()->comment('Файл миниатюры');
            $table->string('title', 255)->comment('Наименование изображения');
            $table->string('description', 255)->comment('Описание изображения');
            $table->comment('Изображения');
            $table->timestamps();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
