<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Pay;
use App\Models\PayMeta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PayController extends Controller
{
    public function show(Pay $pay){
        $pays = Pay::where('id' , $pay->id)->with('carrier','address')->with(["user" => function($q){
            $q->with('userMeta');
        }])->with(["payMeta" => function($q){
            $q->with('post');
        }])->first();
        return Inertia::render('User/ShowPay',[
            'pay' => $pays
        ]);
    }

}
