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
        Schema::create('components', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->unsignedSmallInteger('block_id')->comment('Блок');
            $table->unsignedSmallInteger('tag_id')->comment('Компонент');
            $table->unsignedSmallInteger('position_id')->default(1)->comment('Позиционирование');
            $table->unsignedTinyInteger('npp')->default(1)->comment('Порядковый номер в блоке');
            $table->string('class', 255)->nullable();
            $table->string('element', 255)->nullable()->comment('Тег начала компонента');
            $table->comment('Компоненты блока');
            $table->timestamps();
            $table->primary('id');
            $table->foreign('block_id')
                ->references('id')
                ->on('blocks')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('position_id')
                ->references('id')
                ->on('positions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
