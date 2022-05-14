<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('titleEn')->nullable();
            $table->text('image');
            $table->string('slug');
            $table->smallInteger('count');
            $table->smallInteger('type')->default(0);
            $table->string('product_id' , 15);
            $table->boolean('status')->default(0);
            $table->boolean('variety')->default(0);
            $table->string('suggest',50)->nullable();
            $table->string('score',50)->nullable();
            $table->string('file')->nullable();
            $table->boolean('showcase')->default(0);
            $table->boolean('original')->default(0);
            $table->boolean('used')->default(0);
            $table->string('off' , 3)->nullable();
            $table->text('body')->nullable();
            $table->text('bodyEn')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('price');
            $table->integer('offPrice')->nullable();
            $table->timestamps();
        });
        Schema::create('postables', function (Blueprint $table) {
            $table->integer('post_id');
            $table->integer('postables_id');
            $table->string('postables_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
