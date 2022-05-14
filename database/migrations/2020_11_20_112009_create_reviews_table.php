<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rate' , 400)->nullable();
            $table->text('specifications')->nullable();
            $table->text('ability')->nullable();
            $table->string('size',300)->nullable();
            $table->string('colors' , 500)->nullable();
            $table->text('body')->nullable();
            $table->text('bodyEn')->nullable();
            $table->timestamps();
        });
        Schema::create('reviewables', function (Blueprint $table) {
            $table->integer('review_id');
            $table->integer('reviewables_id');
            $table->string('reviewables_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
