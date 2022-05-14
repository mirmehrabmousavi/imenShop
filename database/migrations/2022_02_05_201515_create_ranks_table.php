<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('off');
            $table->string('from');
            $table->string('to');
            $table->timestamps();
        });
        Schema::create('rankables', function (Blueprint $table) {
            $table->integer('rank_id');
            $table->integer('rankables_id');
            $table->string('rankables_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ranks');
    }
}
