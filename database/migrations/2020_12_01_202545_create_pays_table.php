<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pays', function (Blueprint $table) {
            $table->id();
            $table->string('refId' , 20)->nullable();
            $table->bigInteger('price');
            $table->bigInteger('deposit')->nullable();
            $table->string('auth' , 36)->nullable();
            $table->boolean('status');
            $table->string('time');
            $table->tinyInteger('back')->default(0);
            $table->tinyInteger('method')->default(0);
            $table->boolean('deliver')->default(0);
            $table->string('track')->nullable();
            $table->boolean('seen')->default(0);
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->foreign('discount_id')->references('id')->on('discounts')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('property', 30);
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
        Schema::dropIfExists('pays');
    }
}
