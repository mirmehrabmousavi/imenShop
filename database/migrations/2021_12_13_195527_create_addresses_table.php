<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->string('post' , 10);
            $table->string('name',50);
            $table->string('geo' ,150)->nullable();
            $table->string('state' , 50);
            $table->string('city' , 50);
            $table->tinyInteger('plaque');
            $table->smallInteger('unit')->nullable();
            $table->string('number',11);
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
        Schema::create('addressables', function (Blueprint $table) {
            $table->integer('address_id');
            $table->integer('addressables_id');
            $table->string('addressables_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
