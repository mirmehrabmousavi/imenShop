<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_metas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('gender')->nullable();
            $table->integer('post')->nullable();
            $table->string('address')->nullable();
            $table->string('residenceAddress',150)->nullable();
            $table->integer('code')->nullable();
            $table->string('job' , 50)->nullable();
            $table->string('date' , 11)->nullable();
            $table->timestamps();
        });
        Schema::create('user_metasables', function (Blueprint $table) {
            $table->integer('user_meta_id');
            $table->integer('user_metasables_id');
            $table->string('user_metasables_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_metas');
    }
}
