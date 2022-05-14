<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('nameEn')->nullable();
            $table->text('image')->nullable();
            $table->string('slug');
            $table->timestamps();
        });
        Schema::create('brandables', function (Blueprint $table) {
            $table->integer('brand_id');
            $table->integer('brandables_id');
            $table->string('brandables_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brands');
    }
}
