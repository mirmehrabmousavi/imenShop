<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vidgets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('title' ,800)->nullable();
            $table->string('more')->nullable();
            $table->string('background')->nullable();
            $table->string('titleEn')->nullable();
            $table->string('moreEn')->nullable();
            $table->smallInteger('count')->nullable();
            $table->string('category' , 400)->nullable();
            $table->smallInteger('show')->nullable();
            $table->smallInteger('type')->nullable();
            $table->tinyInteger('platform')->default(0);
            $table->text('brand')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vidgets');
    }
}
