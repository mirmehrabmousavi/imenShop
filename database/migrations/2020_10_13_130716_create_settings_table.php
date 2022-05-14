<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $array = [
            'forbiddens',
            'showUser',
            'limited',
            'languages',
            'map',
            'name',
            'productId',
            'catFooter',
            'catHeader',
            'number',
            'telegram',
            'theme',
            'dark',
            'twitter',
            'instagram',
            'facebook',
            'tokenApp',
            'etemad',
            'fanavari',
            'pages',
            'approved',
            'reply',
            'coercion',
            'checkUser',
            'checkOnline',
            'title',
            'address',
            'about',
            'aboutEn',
            'logo',
            'verify',
            'role',
            'titleSeo',
            'descriptionSeo',
            'keywords',
            'sms',
            'email',
            'showPostCategory',
            'showPostPage',
            'nextPay',
            'zarinbal',
            'idpay',
            'zibal',
            'deposit',
            'choicePay',
            'choicePayApp',
        ];
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();
        });
        foreach ($array as $item) {
            DB::table('settings')->insert(
                array(
                    'key' => $item,
                    'value' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                )
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
