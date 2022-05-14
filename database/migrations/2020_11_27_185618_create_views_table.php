<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('views', function (Blueprint $table) {
            $table->id();
            $table->string('user_ip' , 30);
            $table->string('browser' , 20);
            $table->string('platform' , 20);
            $table->timestamps();
        });
        Schema::create('viewables', function (Blueprint $table) {
            $table->integer('view_id');
            $table->integer('viewables_id');
            $table->string('viewables_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('views');
    }
}
