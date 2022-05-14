<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nameEn');
            $table->bigInteger('price');
            $table->string('city')->nullable();
            $table->integer('limit')->nullable();
            $table->timestamps();
        });
        Schema::create('carriables', function (Blueprint $table) {
            $table->integer('carrier_id');
            $table->integer('carriables_id');
            $table->string('carriables_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carriers');
    }
}
