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
        Schema::create('components_photos', function (Blueprint $table) {
            $table->unsignedSmallInteger('component_id');
            $table->unsignedSmallInteger('photo_id');
            $table->foreign('component_id')->references('id')->on('components')->onDelete('cascade');
            $table->foreign('photo_id')->references('id')->on('photos')->onDelete('cascade');
            $table->primary(['component_id','photo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components_photos');
    }
};
