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
        Schema::create('photos_razdels', function (Blueprint $table) {
            $table->unsignedSmallInteger('photo_id');
            $table->unsignedSmallInteger('razdel_id');
            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');
            $table->foreign('razdel_id')->references('id')->on('razdels')->onDelete('cascade');
            $table->primary(['photo_id','razdel_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos_razdels');
    }
};
