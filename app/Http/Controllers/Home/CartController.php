<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Discount;
use App\Models\Event;
use App\Models\Post;
use App\Models\Rank;
use App\Models\Score;
use App\Models\Setting;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;

class CartController extends Controller
{
    public function changeCarrier(Request $request){
        foreach (auth()->user()->cart as $value) {
            $value->carrier()->detach();
            $value->carrier()->sync($request->carrier);
        }
    }

    public function checkDiscount(Request $request){
        $time = Carbon::now()->format('Y-m-d h:i');
        $dis = Discount::where('code' , $request->discount)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->pluck('code')->first();
        if($dis){
            foreach (auth()->user()->cart as $value) {
                $value->update([
                    'discount' => $dis
                ]);
            }
            return 1;
        }else{
            return 2;
        }
    }

    public function changeTimeDelivery(Request $request){
        foreach (auth()->user()->cart as $value) {
            $value->update([
                'delivery' => $request->time
            ]);
        }
    }

    public function addCart(Request $request)
    {
        $post = Post::where('id', $request->postID)->with('guarantee')->first();
        $colorName = '[]';
        $size = '[]';
        if ($post->review()->pluck('colors')->first()){
            $get1 = json_decode($post->review()->pluck('colors')->first() , true);
            if (count($get1)) {
                if ($get1[0]['count'] >= 1){
                    $colorName = json_encode($get1[0]);
                }elseif($get1[1]){
                    $colorName = json_encode($get1[1]);
                }else{
                    return 'limit';
                }
            }
        }
        if ($post->review()->pluck('size')->first()) {
            $get2 = json_decode($post->review()->pluck('size')->first() , true);
            if (count($get2)) {
                if ($get2[0]['count'] >= 1){
                    $size = json_encode($get2[0]);
                }elseif($get2[1]){
                    $size = json_encode($get2[1]);
                }else{
                    return 'limit';
                }
            }
        }
        if (auth()->user()) {
            $cart = Cart::where('post_id', $request->postID)->where('guarantee_id', $post['guarantee'][0]['id'])->where('user_id', auth()->user()->id)->where('color', $colorName)->where('size', $size)->get();
            if (count($cart) >= 1) {
                $countCheck = Post::where('id', $cart[0]->post_id)->pluck('count')->first();
                $cartCount = 0;
                for ($i = 0; $i < count($cart); $i++) {
                    $cartCount = $cartCount + $cart[$i]->count;
                }
            } else {
                $countCheck = 100;
                $cartCount = 0;
            }
            if ($countCheck - $cartCount >= 1) {
                $check = Cart::where('post_id', $request->postID)->where('guarantee_id', $post['guarantee'][0]['id'])->where('user_id', auth()->user()->id)->where('color', $colorName)->where('size', $size)->first();
                if ($check) {
                    $check->update([
                        'count' => ++$check->count
                    ]);
                    return 'success';
                } else {
                    Cart::create([
                        'post_id' => $request->postID,
                        'user_id' => auth()->user()->id,
                        'color' => $colorName,
                        'size' => $size,
                        'guarantee_id' => $post['guarantee'][0]['id'],
                        'price' => $post->price,
                        'count' => 1,
                    ]);
                    Event::create([
                        'type' => 3,
                        'title' => 'سبد خرید',
                        'user_id' => auth()->user()->id,
                        'description' => 'کاربر با نام ' . auth()->user()->name . ' محصول ' . $post->title . ' را به سبد خرید اضافه کرد',
                    ]);
                }
            } else {
                return 'limit';
            }
        } else {
            return 'no';
        }
    }

    public function addCart2(Request $request)
    {
        if (auth()->user()) {
            $cart = Cart::where('post_id', $request->postID)->where('guarantee_id', $request->guarantee)->where('size', json_encode($request->sizeName))->where('color', json_encode($request->colorName))->where('user_id', auth()->user()->id)->get();
            if (count($cart) >= 1) {
                $countCheck = Post::where('id', $cart[0]->post_id)->pluck('count')->first();
                $cartCount = 0;
                for ($i = 0; $i < count($cart); $i++) {
                    $cartCount = $cartCount + $cart[$i]->count;
                }
            } else {
                $countCheck = 100;
                $cartCount = 0;
            }
            if ($countCheck - $cartCount >= 1) {
                $check = Cart::where('post_id', $request->postID)->where('size', json_encode($request->sizeName))->where('color', json_encode($request->colorName))->where('guarantee_id', $request->guarantee)->where('user_id', auth()->user()->id)->first();
                if ($check) {
                    $check->update([
                        'count' => ++$check->count
                    ]);
                    return 'success';
                } else {
                    Cart::create([
                        'post_id' => $request->postID,
                        'user_id' => auth()->user()->id,
                        'guarantee_id' => $request->guarantee,
                        'color' => json_encode($request->colorName),
                        'size' => json_encode($request->sizeName),
                        'price' => $request->price,
                        'count' => 1,
                    ]);
                    $post = Post::where('id', $request->postID)->pluck('title')->first();
                    Event::create([
                        'type' => 3,
                        'title' => 'سبد خرید',
                        'user_id' => auth()->user()->id,
                        'description' => 'کاربر با نام ' . auth()->user()->name . ' محصول ' . $post . ' را به سبد خرید اضافه کرد',
                    ]);
                }
            } else {
                return 'limit';
            }
        } else {
            return 'no';
        }
    }

    public function getCarts()
    {
        if (auth()->user()) {
            $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier','user')->get();
            $cart = auth()->user()->cart()->pluck('post_id');
            $carts = [];
            foreach ($cart as $item) {
                $send = Post::where('id', $item)->first();
                array_push($carts, $send);
            };
            return [$carts, $count];
        } else {
            return 'no';
        }
    }

    public function changeCarts(Cart $cart, Request $request)
    {
        if ($request->change == 0) {
            if ($cart->count == 1) {
                $cart->delete();
            } else {
                $cart->update([
                    'count' => --$cart->count
                ]);
            }
            return 'success';
        } else {
            $post = Post::where('id', $cart->post_id)->pluck('count')->first();
            if ($post - $cart->count >= 1) {
                $cart->update([
                    'count' => ++$cart->count
                ]);
                return 'success';
            } else {
                return 'limit';
            }
        }
    }

    public function deleteCart(Cart $cart)
    {
        $cart->delete();
        return 'success';
    }

    public function cart()
    {
        $title = Setting::where('key', 'title')->pluck('value')->first();
        $body = Setting::where('key', 'body')->pluck('value')->first();
        $address = Setting::where('key', 'address')->pluck('value')->first();
        $keyword = Setting::where('key', 'keyword')->pluck('value')->first();
        $logo = Setting::where('key', 'logo')->pluck('value')->first();
        OpenGraph::setDescription($body);
        OpenGraph::addProperty('locale', 'fa-ir');
        OpenGraph::setUrl($address);
        SEOTools::opengraph()->addProperty('type', 'stores');
        SEOMeta::addKeyword($keyword);
        OpenGraph::addImage($address . $logo);
        OpenGraph::setSiteName($title);
        TwitterCard::setTitle($title);
        TwitterCard::setDescription($body);
        TwitterCard::setUrl($address);
        TwitterCard::addImage($address . $logo);
        if(auth()->user()){
            $count = Cart::where('user_id' , auth()->user()->id)->get();
            for ($i = 0; $i < count($count); $i++) {
                $countCheck = Post::where('id', $count[$i]->post_id)->pluck('count')->first();
                $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                $postSize = [];
                $postColor = [];
                $myScore = Score::latest()->where('type' , 0)->where('user_id' , auth()->user()->id)->pluck('name')->sum();
                $myRank = Rank::where('from' , '<=', $myScore)->where('to' , '>=', $myScore)->first();
                if($myRank){
                    $myRankPost = $myRank->post()->where('id' , $post->id)->first();
                    if($myRankPost){
                        $finalPrice = round($myRankPost->offPrice - $myRankPost->offPrice * $myRank->off / 100);
                    }else{
                        $finalPrice = $post->price;
                    }
                }else{
                    $finalPrice = $post->price;
                }
                $price = $finalPrice;
                if ($count[$i]['color'] != '[]'){
                    $cartColor = json_decode($count[$i]['color'],true)['name'];
                    foreach (json_decode($post['review'][0]['colors'] , true) as $item) {
                        if($item['name'] == $cartColor){
                            $postColor = $item;
                            $price = $price + $postColor['price'];
                            $count[$i]->update([
                                'color' => json_encode($postColor),
                            ]);
                            if($postColor['count'] <=0){
                                $count[$i]->update([
                                    'color' => 'empty',
                                ]);
                            }
                        }
                    }
                    if ($postColor == []){
                        $count[$i]->update([
                            'color' => 'empty',
                        ]);
                    }
                }
                if ($count[$i]['size'] != '[]'){
                    $cartSize = json_decode($count[$i]['size'],true)['name'];
                    foreach (json_decode($post['review'][0]['size'] , true) as $item) {
                        if($item['name'] == $cartSize){
                            $postSize = $item;
                            $price = $price + $postSize['price'];
                            $count[$i]->update([
                                'size' => json_encode($postSize),
                            ]);
                            if($postSize['count'] <= 0){
                                $count[$i]->update([
                                    'size' => 'empty',
                                ]);
                            }
                        }
                    }
                    if ($postSize == []){
                        $count[$i]->update([
                            'size' => 'empty',
                        ]);
                    }
                }
                $count[$i]->update([
                    'price' => $price,
                ]);
                if ($countCheck - $count[$i]->count < 0 || $count[$i]['size'] == 'empty' || $count[$i]['color'] == 'empty') {
                    $count[$i]->delete();
                }
            };
        }
        return Inertia::render('Cart/CartIndex', [
            'title' => $title,
        ]);
    }
}
