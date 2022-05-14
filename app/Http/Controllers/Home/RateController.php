<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Post;
use App\Models\Rate;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function store(Request $request){
        if (auth()->user()){
            $rate = Rate::where('post_id' , $request->post_id)->where('user_id' , auth()->user()->id)->pluck('rate_post')->first();
            $rates=[];
            $array = Rate::where('post_id' , $request->post_id)->pluck('rate_post');
            for ( $i = 0; $i < count($array); $i++) {
                $oneRate = $array[$i];
                array_push($rates ,$request->rate_post);
                array_push($rates ,$oneRate);
            }
            if ($rates != []){
                $average2 = array_sum($rates)/count($rates);
                $average = number_format($average2 , 1);
            }else{
                $average = $request->rate_post;
            }
            if (!$rate){
                $post = Post::where('id', $request->post_id)->pluck('title')->first();
                Rate::create([
                    'user_id'=>auth()->user()->id,
                    'post_id'=>$request->post_id,
                    'rate_post'=>$request->rate_post,
                ]);
                Event::create([
                    'type' => 6,
                    'title' => 'امتیاز دادن',
                    'user_id' => auth()->user()->id,
                    'description' => 'کاربر با نام ' . auth()->user()->name . ' به محصول ' . $post . ' امتیاز داد',
                ]);
                return [$request->rate_post , $average , count($array)];
            }else{
                return 'found';
            }
        }else{
            return 'noUser';
        }
    }
    public function getRate(Request $request){
        if (auth()->user()){
            $rate = Rate::where('post_id' , $request->post_id)->where('user_id' , auth()->user()->id)->first();
            $rates=[];
            $array = Rate::where('post_id' , $request->post_id)->pluck('rate_post');
            for ( $i = 0; $i < count($array); $i++) {
                $oneRate = $array[$i];
                array_push($rates ,$oneRate);
            }
            if ($rates != []){
                $average2 = array_sum($rates)/count($rates);
                $average = number_format($average2 , 1);
            }else{
                $average = '0';
            }
            if ($rate){
                return [$rate->rate_post , $average , count($array)];
            }else{
                return ['0' , $average , count($array)];
            }
        }else{
            $rates=[];
            $array = Rate::where('post_id' , $request->post_id)->pluck('rate_post');
            for ( $i = 0; $i < count($array); $i++) {
                $oneRate = $array[$i];
                array_push($rates ,$oneRate);
            }
            if ($rates != []){
                $average2 = array_sum($rates)/count($rates);
                $average = number_format($average2 , 1);
            }else{
                $average = '0';
            }
            return ['0' , $average , count($array)];
        }
    }
}
