<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\lib\idpay;
use App\lib\nextpay;
use App\lib\zarinpal;
use App\lib\zibal;
use App\Models\Cart;
use App\Models\Charge;
use App\Models\Discount;
use App\Models\Pay;
use App\Models\PayMeta;
use App\Models\Post;
use App\Models\Rank;
use App\Models\Score;
use App\Models\Setting;
use App\Traits\SendEmailTrait;
use App\Traits\SendSmsTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Melipayamak\MelipayamakApi;
use nusoap_client;

class ShopController extends Controller
{
    use SendSmsTrait;
    use SendEmailTrait;
    public function addCharge(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:5000',
        ]);
        $choicePay = Setting::where('key' , 'choicePay')->pluck('value')->first();
        if (auth()->user()) {
            $number = auth()->user()->pluck('number')->first();
            auth()->user()->update([
                'buy'=> $request->amount
            ]);
            $amount = $request->amount;
            if($choicePay == 0){
                $order = new zarinpal();
                $res = $order->pay($amount,auth()->user()->email,$number , '/charge/order');
                return redirect('https://sandbox.zarinpal.com/pg/StartPay/' . $res);
            }
            if($choicePay == 1){
                $order = new zibal();
                $res = $order->pay($amount,auth()->user()->email,$number , '/charge/order/zibal');
                return redirect('https://gateway.zibal.ir/start/' . $res);
            }
            if($choicePay == 2){
                $order = new nextpay();
                $res = $order->pay($amount,auth()->user()->email,$number , '/charge/order/nextPay');
                return redirect("https://nextpay.org/nx/gateway/payment/".$res);
            }
            if($choicePay == 3){
                $order = new idpay();
                $res = $order->pay($amount,auth()->user()->email,$number , '/charge/order/idpay');
                return redirect("https://idpay.ir/p/ws-sandbox/".$res);
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function add_order()
    {
        $time = Carbon::now()->format('Y-m-d h:i');
        $choicePay = Setting::where('key' , 'choicePay')->pluck('value')->first();
        if (auth()->user()->cart()->count() >= 1) {
            $cart = auth()->user()->cart;
            $address = auth()->user()->address()->where('status' , 1)->first();
            if($address){
                if($cart[0]->delivery && $cart[0]->carrier){
                    $number = auth()->user()->pluck('number')->first();
                    $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier')->get();
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

                    $amount = 0;
                    for ( $i = 0; $i < count($count); $i++) {
                        $allSum2 = (int)$count[$i]['price'] * (int)$count[$i]['count'];
                        $amount = $amount + (int)$allSum2;
                        if($count[$i]->discount){
                            $discount = Discount::where('code' , $count[$i]->discount)->where('post_id', '!=' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if ($discount) {
                                if($count[$i]['post_id'] == $discount['post_id']){
                                    $amount = $amount - (($amount * $discount->percent) / 100);
                                }
                            }
                        }
                    }

                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $amount = (int)$amount + (int)$sends;

                    if($count[0]->discount){
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $amount = ($amount * $discount->percent) / 100;
                        }
                    }

                    if($choicePay == 0){
                        $order = new zarinpal();
                        $res = $order->pay($amount,auth()->user()->email,$number , '/order');
                        return redirect('https://sandbox.zarinpal.com/pg/StartPay/' . $res);
                    }
                    if($choicePay == 1){
                        $order = new zibal();
                        $res = $order->pay($amount,auth()->user()->email,$number , '/order/zibal');
                        return redirect('https://gateway.zibal.ir/start/' . $res);
                    }
                    if($choicePay == 2){
                        $order = new nextpay();
                        $res = $order->pay($amount,auth()->user()->email,$number , '/order/nextPay');
                        return redirect("https://nextpay.org/nx/gateway/payment/".$res);
                    }
                    if($choicePay == 3){
                        $order = new idpay();
                        $res = $order->pay($amount,auth()->user()->email,$number , '/order/idpay');
                        return redirect("https://idpay.ir/p/ws-sandbox/".$res);
                    }
                }
                else{
                    return redirect('/user-info-cart');
                }
            }else{
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function paymentSpot()
    {
        $time = Carbon::now()->format('Y-m-d h:i');
        $choicePay = Setting::where('key' , 'choicePay')->pluck('value')->first();
        if (auth()->user()->cart()->count() >= 1) {
            $cart = auth()->user()->cart;
            $address = auth()->user()->address()->where('status' , 1)->first();
            if($address){
                if($cart[0]->delivery && $cart[0]->carrier){
                    $number = auth()->user()->pluck('number')->first();
                    $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier')->get();
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

                    $amount = 0;
                    for ( $i = 0; $i < count($count); $i++) {
                        $allSum2 = (int)$count[$i]['price'] * (int)$count[$i]['count'];
                        $amount = $amount + (int)$allSum2;
                        if($count[$i]->discount){
                            $discount = Discount::where('code' , $count[$i]->discount)->where('post_id', '!=' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if ($discount) {
                                if($count[$i]['post_id'] == $discount['post_id']){
                                    $amount = $amount - (($amount * $discount->percent) / 100);
                                }
                            }
                        }
                    }

                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $amount = (int)$amount + (int)$sends;

                    if($count[0]->discount){
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $amount = ($amount * $discount->percent) / 100;
                        }
                    }
                    $deposit = Setting::where('key' , 'deposit')->pluck('value')->first();
                    $amountA = round($amount * (int)$deposit / 100);

                    if($choicePay == 0){
                        $order = new zarinpal();
                        $res = $order->pay($amountA,auth()->user()->email,$number , '/spot/order');
                        return redirect('https://sandbox.zarinpal.com/pg/StartPay/' . $res);
                    }
                    if($choicePay == 1){
                        $order = new zibal();
                        $res = $order->pay($amountA,auth()->user()->email,$number , '/spot/zibal');
                        return redirect('https://gateway.zibal.ir/start/' . $res);
                    }
                    if($choicePay == 2){
                        $order = new nextpay();
                        $res = $order->pay($amountA,auth()->user()->email,$number , '/spot/nextPay');
                        return redirect("https://nextpay.org/nx/gateway/payment/".$res);
                    }
                    if($choicePay == 3){
                        $order = new idpay();
                        $res = $order->pay($amountA,auth()->user()->email,$number , '/spot/idpay');
                        return redirect("https://idpay.ir/p/ws-sandbox/".$res);
                    }
                }
                else{
                    return redirect('/user-info-cart');
                }
            }else{
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function shopWallet()
    {
        $time = Carbon::now()->format('Y-m-d h:i');
        $name = auth()->user()->name;
        if (auth()->user()->cart()->count() >= 1) {
            $cart = auth()->user()->cart;
            $address = auth()->user()->address()->where('status' , 1)->first();
            if($address){
                if($cart[0]->delivery && $cart[0]->carrier){
                    $number = auth()->user()->pluck('number')->first();
                    $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier')->get();
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

                    $amount = 0;
                    for ( $i = 0; $i < count($count); $i++) {
                        $allSum2 = (int)$count[$i]['price'] * (int)$count[$i]['count'];
                        $amount = $amount + (int)$allSum2;
                        if($count[$i]->discount){
                            $discount = Discount::where('code' , $count[$i]->discount)->where('post_id', '!=' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if ($discount) {
                                if($count[$i]['post_id'] == $discount['post_id']){
                                    $amount = $amount - (($amount * $discount->percent) / 100);
                                }
                            }
                        }
                    }

                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $amount = (int)$amount + (int)$sends;
                    if($count[0]->discount){
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $amount = ($amount * $discount->percent) / 100;
                        }
                    }
                    $walletIncrease = Charge::latest()->where('type' , 0)->where('user_id' , auth()->user()->id)->where('status' , 100)->pluck('price')->sum();
                    $walletDecrease = Charge::latest()->where('type' , 1)->where('user_id' , auth()->user()->id)->where('status' , 100)->pluck('price')->sum();
                    $wallet = $walletIncrease - $walletDecrease;
                    if($wallet >= $amount){
                        $chars = '012345678901234567890123456789';
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Pay::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                            }
                        }
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $discountId = $discount->id;
                            $discount->update([
                                'count'=> --$discount->count
                            ]);
                        }else{
                            $discountId = null;
                        }
                        $pay = Pay::create([
                            'refId'=>'کیف پول',
                            'status'=> 100,
                            'time' => $count[0]->delivery,
                            'property'=>$chars2,
                            'price'=>$amount,
                            'method'=>1,
                            'discount_id'=>$discountId,
                            'user_id'=>auth()->user()->id,
                            'auth'=>'کیف پول',
                        ]);
                        Charge::create([
                            'refId'=>'کیف پول',
                            'status'=> 100,
                            'type' => 1,
                            'property'=>$chars2,
                            'price'=>$amount,
                            'method'=>1,
                            'user_id'=>auth()->user()->id,
                        ]);
                        $pay->address()->attach($address->id);
                        $pay->carrier()->sync($cart[0]['carrier'][0]['id']);
                        for ( $i = 0; $i < count($count); $i++) {
                            $count2 = Post::where('id' , $count[$i]->post_id)->pluck('count')->first();
                            Post::where('id' , $count[$i]->post_id)->first()->update([
                                'count' => $count2 - $count[$i]->count
                            ]);
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                                $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                            }else{
                                $discountId = null;
                                $getPrice = $count[$i]->price * $count[$i]->count;
                            }
                            $payMeta = PayMeta::create([
                                'post_id' => $count[$i]->post_id,
                                'user_id' => $count[$i]->user_id,
                                'pay_id' => $pay->id,
                                'discount_id' => $discountId,
                                'status'=>100,
                                'method'=>1,
                                'price'=>$getPrice,
                                'count' => $count[$i]->count,
                                'color' => $count[$i]->color,
                                'size' => $count[$i]->size,
                            ]);
                            $payMeta->address()->attach($address->id);
                            $payMeta->guarantee()->sync($count[$i]->guarantee_id);

                            $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                            if ($count[$i]['color'] != '[]'){
                                $cartColor = json_decode($count[$i]['color'],true)['name'];
                                $colors = [];
                                foreach (json_decode($post['review'][0]['colors'] , true) as $item) {
                                    if($item['name'] == $cartColor){
                                        $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                    }
                                    array_push($colors , $item);
                                }
                                $post->review()->first()->update([
                                    'colors' => json_encode($colors),
                                ]);
                            }
                            if ($count[$i]['size'] != '[]'){
                                $cartSize = json_decode($count[$i]['size'],true)['name'];
                                $sizes = [];
                                foreach (json_decode($post['review'][0]['size'] , true) as $item) {
                                    if($item['name'] == $cartSize){
                                        $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                    }
                                    array_push($sizes , $item);
                                }
                                $post->review()->first()->update([
                                    'size' => json_encode($sizes),
                                ]);
                            }
                        }
                        auth()->user()->cart()->delete();
                        $address = Setting::where('key' , 'address')->pluck('value')->first();
                        $link = $address."show-pay/$pay->property";
                        if(auth()->user()->email){
                            $text2 = "<strong>سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به آدرس زیر برین : </strong><br/> <a href='$link'>پیگیری پرداختی</a>";
                            $this->sendEmail(auth()->user()->email , $text2 , 'خرید موفقیت آمیز');
                        }
                        if(auth()->user()->number){
                            $text2 = "سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به پنل کاربریتان مراجعه کنید";
                            $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                            if($sms == 1){
                                $username = '';
                                $password = '';
                                $api = new MelipayamakApi($username,$password);
                                $sms = $api->sms();
                                $to = auth()->user()->number;
                                $from = '';
                                $text = $text2;
                                $response = $sms->send($to,$from,$text);
                            }else{
                                $this->sendSms(auth()->user()->number , $text2,env('GHASEDAKAPI_Number'));
                            }
                        }

                        $score = 0;
                        for ( $i = 0; $i < count($count); $i++) {
                            $allSum2 = (int)$count[$i]['score'] * (int)$count[$i]['count'];
                            $score = $score + (int)$allSum2;
                        }
                        if($score >= 1){
                            Score::create([
                                'name'=>$score,
                                'user_id'=>auth()->user()->id,
                            ]);
                        }
                        return Inertia::render('Cart/BuyIndex' , [
                            'name' => $name,
                            'pay' => $pay,
                        ]);
                    }else{
                        $chars = '012345678901234567890123456789';
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Pay::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                            }
                        }
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $discountId = $discount->id;
                            $discount->update([
                                'count'=> --$discount->count
                            ]);
                        }else{
                            $discountId = null;
                        }
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>0,
                            'price'=>$amount,
                            'property'=>$chars2,
                            'method'=>1,
                            'time' => $count[0]->delivery,
                            'discount_id' => $discountId,
                            'user_id'=>auth()->user()->id,
                            'auth' => 'کیف پول',
                        ]);
                        Charge::create([
                            'refId'=>'کیف پول',
                            'status'=> 0,
                            'type' => 1,
                            'property'=>$chars2,
                            'price'=>$amount,
                            'user_id'=>auth()->user()->id,
                        ]);
                        for ( $i = 0; $i < count($count); $i++) {
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                            }else{
                                $discountId = null;
                                $getPrice = $count[$i]->price * $count[$i]->count;
                            }
                            $payMeta = PayMeta::create([
                                'post_id' => $count[$i]->post_id,
                                'user_id' => $count[$i]->user_id,
                                'status'=>0,
                                'pay_id' => $pay->id,
                                'price'=> $getPrice,
                                'method'=>1,
                                'discount_id' => $discountId,
                                'count' => $count[$i]->count,
                                'color' => $count[$i]->color,
                                'size' => $count[$i]->size,
                            ]);
                            $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                        }
                        return Inertia::render('Cart/BuyIndex' , [
                            'name' => $name,
                            'pay' => $pay,
                        ]);
                    }
                }
                else{
                    return redirect('/user-info-cart');
                }
            }else{
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function order(Request $request)
    {
        $time = Carbon::now()->format('Y-m-d h:i');
        $MerchantID = Setting::where('key' , 'zarinpal')->pluck('value')->first();
        if (auth()->user()->cart()->count() >= 1) {
            $cart = auth()->user()->cart;
            $address = auth()->user()->address()->where('status' , 1)->first();
            if($address){
                if($cart[0]->delivery && $cart[0]->carrier){
                    $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier')->get();
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

                    $Amount = 0;
                    for ( $i = 0; $i < count($count); $i++) {
                        $allSum2 = (int)$count[$i]['price'] * (int)$count[$i]['count'];
                        $Amount = $Amount + (int)$allSum2;
                        if($count[$i]->discount){
                            $discount = Discount::where('code' , $count[$i]->discount)->where('post_id', '!=' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if ($discount) {
                                if($count[$i]['post_id'] == $discount['post_id']){
                                    $Amount = $Amount - (($Amount * $discount->percent) / 100);
                                }
                            }
                        }
                    }

                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $Amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $Amount = (int)$Amount + (int)$sends;
                    if($count[0]->discount){
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $Amount = ($Amount * $discount->percent) / 100;
                        }
                    }

                    $Authority =$request->get('Authority');
                    $v = verta();
                    $name = auth()->user()->name;
                    if ($request->get('Status') == 'OK') {
                        $client = new nusoap_client('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
                        $client->soap_defencoding = 'UTF-8';

                        $result = $client->call('PaymentVerification', [
                            [
                                'MerchantID'     => $MerchantID,
                                'Authority'      => $Authority,
                                'Amount'         => $Amount,
                            ],
                        ]);

                        if ($result['Status'] == 100) {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>$result['RefID'],
                                'status'=>$result['Status'],
                                'property'=>$chars2,
                                'time' => $count[0]->delivery,
                                'price'=>$Amount,
                                'discount_id'=>$discountId,
                                'user_id'=>auth()->user()->id,
                                'auth'=>$Authority,
                            ]);
                            $pay->carrier()->sync($cart[0]['carrier'][0]['id']);
                            $pay->address()->attach($address->id);
                            for ( $i = 0; $i < count($count); $i++) {
                                $count2 = Post::where('id' , $count[$i]->post_id)->pluck('count')->first();
                                Post::where('id' , $count[$i]->post_id)->first()->update([
                                    'count' => $count2 - $count[$i]->count
                                ]);
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $discount->update([
                                        'count'=> --$discount->count
                                    ]);
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'pay_id' => $pay->id,
                                    'discount_id'=>$discountId,
                                    'status'=>$result['Status'],
                                    'price'=> $getPrice,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);

                                $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                                if ($count[$i]['color'] != '[]'){
                                    $cartColor = json_decode($count[$i]['color'],true)['name'];
                                    $colors = [];
                                    foreach (json_decode($post['review'][0]['colors'] , true) as $item) {
                                        if($item['name'] == $cartColor){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($colors , $item);
                                    }
                                    $post->review()->first()->update([
                                        'colors' => json_encode($colors),
                                    ]);
                                }
                                if ($count[$i]['size'] != '[]'){
                                    $cartSize = json_decode($count[$i]['size'],true)['name'];
                                    $sizes = [];
                                    foreach (json_decode($post['review'][0]['size'] , true) as $item) {
                                        if($item['name'] == $cartSize){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($sizes , $item);
                                    }
                                    $post->review()->first()->update([
                                        'size' => json_encode($sizes),
                                    ]);
                                }
                                $score = 0;
                                for ( $i = 0; $i < count($count); $i++) {
                                    $allSum2 = (int)$post['score'] * (int)$count[$i]['count'];
                                    $score = $score + (int)$allSum2;
                                }
                            }
                            auth()->user()->cart()->delete();
                            $address = Setting::where('key' , 'address')->pluck('value')->first();
                            $link = $address."show-pay/$pay->property";
                            if(auth()->user()->email){
                                $text2 = "<strong>سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به آدرس زیر برین : </strong><br/> <a href='$link'>پیگیری پرداختی</a>";
                                $this->sendEmail(auth()->user()->email , $text2 , 'خرید موفقیت آمیز');
                            }
                            if(auth()->user()->number){
                                $text2 = "سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به پنل کاربریتان مراجعه کنید";
                                $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                                if($sms == 1){
                                    $username = '';
                                    $password = '';
                                    $api = new MelipayamakApi($username,$password);
                                    $sms = $api->sms();
                                    $to = auth()->user()->number;
                                    $from = '';
                                    $text = $text2;
                                    $response = $sms->send($to,$from,$text);
                                }else{
                                    $this->sendSms(auth()->user()->number , $text2,env('GHASEDAKAPI_Number'));
                                }
                            }
                            if($score >= 1){
                                Score::create([
                                    'name'=>$score,
                                    'user_id'=>auth()->user()->id,
                                ]);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }else {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>'',
                                'status'=>$result['Status'],
                                'property'=>$chars2,
                                'time' => $count[0]->delivery,
                                'user_id'=>auth()->user()->id,
                                'discount'=> $discountId,
                                'price'=>$Amount,
                                'auth' => $request->Authority,
                            ]);
                            $pay->address()->attach($address->id);
                            for ( $i = 0; $i < count($count); $i++) {
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'status'=> $result['Status'],
                                    'discount'=> $discountId,
                                    'pay_id' => $pay->id,
                                    'price'=> $getPrice,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }
                    }
                    else{
                        $chars = '012345678901234567890123456789';
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Pay::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                            }
                        }
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $discountId = $discount->id;
                        }else{
                            $discountId = null;
                        }
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>0,
                            'price'=>$Amount,
                            'time' => $count[0]->delivery,
                            'property'=>$chars2,
                            'discount_id'=> $discountId,
                            'user_id'=>auth()->user()->id,
                            'auth' => $request->Authority,
                        ]);
                        $pay->address()->attach($address->id);
                        for ( $i = 0; $i < count($count); $i++) {
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                            }else{
                                $discountId = null;
                                $getPrice = $count[$i]->price * $count[$i]->count;
                            }
                            $payMeta = PayMeta::create([
                                'post_id' => $count[$i]->post_id,
                                'user_id' => $count[$i]->user_id,
                                'status'=>0,
                                'pay_id' => $pay->id,
                                'price'=> $getPrice,
                                'discount_id'=> $discountId,
                                'count' => $count[$i]->count,
                                'color' => $count[$i]->color,
                                'size' => $count[$i]->size,
                            ]);
                            $payMeta->address()->attach($address->id);
                            $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                        }
                        return Inertia::render('Cart/BuyIndex' , [
                            'name' => $name,
                            'pay' => $pay,
                        ]);
                    }
                }else {
                    return redirect('/user-info-cart');
                }
            }else{
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function nextPay(Request $request){
        $time = Carbon::now()->format('Y-m-d h:i');
        $MerchantID = Setting::where('key' , 'nextPay')->pluck('value')->first();
        if (auth()->user()->cart()->count() >= 1) {
            $cart = auth()->user()->cart;
            $address = auth()->user()->address()->where('status' , 1)->first();
            if($address){
                if($cart[0]->delivery && $cart[0]->carrier){
                    $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier')->get();
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

                    $Amount = 0;
                    for ( $i = 0; $i < count($count); $i++) {
                        $allSum2 = (int)$count[$i]['price'] * (int)$count[$i]['count'];
                        $Amount = $Amount + (int)$allSum2;
                        if($count[$i]->discount){
                            $discount = Discount::where('code' , $count[$i]->discount)->where('post_id', '!=' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if ($discount) {
                                if($count[$i]['post_id'] == $discount['post_id']){
                                    $Amount = $Amount - (($Amount * $discount->percent) / 100);
                                }
                            }
                        }
                    }

                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $Amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $Amount = (int)$Amount + (int)$sends;
                    if($count[0]->discount){
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $Amount = ($Amount * $discount->percent) / 100;
                        }
                    }

                    $Authority =$request->trans_id;
                    $v = verta();
                    $name = auth()->user()->name;
                    if ($request->np_status != 'Unsuccessful') {
                        $client = new Client();
                        $response = $client->request('POST', 'https://nextpay.org/nx/gateway/verify',
                            [
                                'form_params' => [
                                    'api_key' => $MerchantID,
                                    'amount' => $Amount,
                                    'trans_id' => $Authority,
                                ],
                                'allow_redirects' => true
                            ]);

                        $contents = $response->getBody()->getContents();
                        $contents = json_decode($contents,true);


                        if ($contents['code'] == 0) {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>$contents['Shaparak_Ref_Id'],
                                'status'=> 100,
                                'property'=>$chars2,
                                'time' => $count[0]->delivery,
                                'price'=>$Amount,
                                'discount_id'=>$discountId,
                                'user_id'=>auth()->user()->id,
                                'auth'=>$Authority,
                            ]);
                            $pay->address()->attach($address->id);
                            $pay->carrier()->sync($cart[0]['carrier'][0]['id']);
                            for ( $i = 0; $i < count($count); $i++) {
                                $count2 = Post::where('id' , $count[$i]->post_id)->pluck('count')->first();
                                Post::where('id' , $count[$i]->post_id)->first()->update([
                                    'count' => $count2 - $count[$i]->count
                                ]);
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $discount->update([
                                        'count'=> --$discount->count
                                    ]);
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'pay_id' => $pay->id,
                                    'discount_id' => $discountId,
                                    'status'=>100,
                                    'price'=> $getPrice,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);

                                $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                                if ($count[$i]['color'] != '[]'){
                                    $cartColor = json_decode($count[$i]['color'],true)['name'];
                                    $colors = [];
                                    foreach (json_decode($post['review'][0]['colors'] , true) as $item) {
                                        if($item['name'] == $cartColor){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($colors , $item);
                                    }
                                    $post->review()->first()->update([
                                        'colors' => json_encode($colors),
                                    ]);
                                }
                                if ($count[$i]['size'] != '[]'){
                                    $cartSize = json_decode($count[$i]['size'],true)['name'];
                                    $sizes = [];
                                    foreach (json_decode($post['review'][0]['size'] , true) as $item) {
                                        if($item['name'] == $cartSize){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($sizes , $item);
                                    }
                                    $post->review()->first()->update([
                                        'size' => json_encode($sizes),
                                    ]);
                                }
                                $score = 0;
                                for ( $i = 0; $i < count($count); $i++) {
                                    $allSum2 = (int)$post['score'] * (int)$count[$i]['count'];
                                    $score = $score + (int)$allSum2;
                                }
                            }
                            auth()->user()->cart()->delete();
                            $address = Setting::where('key' , 'address')->pluck('value')->first();
                            $link = $address."show-pay/$pay->property";
                            if(auth()->user()->email){
                                $text2 = "<strong>سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به آدرس زیر برین : </strong><br/> <a href='$link'>پیگیری پرداختی</a>";
                                $this->sendEmail(auth()->user()->email , $text2 , 'خرید موفقیت آمیز');
                            }
                            if(auth()->user()->number){
                                $text2 = "سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به پنل کاربریتان مراجعه کنید";
                                $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                                if($sms == 1){
                                    $username = '';
                                    $password = '';
                                    $api = new MelipayamakApi($username,$password);
                                    $sms = $api->sms();
                                    $to = auth()->user()->number;
                                    $from = '';
                                    $text = $text2;
                                    $response = $sms->send($to,$from,$text);
                                }else{
                                    $this->sendSms(auth()->user()->number , $text2,env('GHASEDAKAPI_Number'));
                                }
                            }
                            if($score >= 1){
                                Score::create([
                                    'name'=>$score,
                                    'user_id'=>auth()->user()->id,
                                ]);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }else {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>'',
                                'status'=>$contents['code'],
                                'property'=>$chars2,
                                'time' => $count[0]->delivery,
                                'user_id'=>auth()->user()->id,
                                'discount_id' => $discountId,
                                'price'=>$Amount,
                                'auth' => $Authority,
                            ]);
                            $pay->address()->attach($address->id);
                            for ( $i = 0; $i < count($count); $i++) {
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'status'=>$contents['code'],
                                    'pay_id' => $pay->id,
                                    'discount_id' => $discountId,
                                    'price'=> $getPrice,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }
                    }
                    else{
                        $chars = '012345678901234567890123456789';
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Pay::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                            }
                        }
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $discountId = $discount->id;
                        }else{
                            $discountId = null;
                        }
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>0,
                            'time' => $count[0]->delivery,
                            'price'=>$Amount,
                            'property'=>$chars2,
                            'discount_id' => $discountId,
                            'user_id'=>auth()->user()->id,
                            'auth' => $Authority,
                        ]);
                        $pay->address()->attach($address->id);
                        for ( $i = 0; $i < count($count); $i++) {
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                            }else{
                                $discountId = null;
                                $getPrice = $count[$i]->price * $count[$i]->count;
                            }
                            $payMeta = PayMeta::create([
                                'post_id' => $count[$i]->post_id,
                                'user_id' => $count[$i]->user_id,
                                'status'=>0,
                                'pay_id' => $pay->id,
                                'discount_id' => $discountId,
                                'price'=> $getPrice,
                                'count' => $count[$i]->count,
                                'color' => $count[$i]->color,
                                'size' => $count[$i]->size,
                            ]);
                            $payMeta->address()->attach($address->id);
                            $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                        }
                        return Inertia::render('Cart/BuyIndex' , [
                            'name' => $name,
                            'pay' => $pay,
                        ]);
                    }
                }else {
                    return redirect('/user-info-cart');
                }
            }else{
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function zibal(Request $request){
        $time = Carbon::now()->format('Y-m-d h:i');
        $MerchantID = Setting::where('key' , 'zibal')->pluck('value')->first();
        if (auth()->user()->cart()->count() >= 1) {
            $cart = auth()->user()->cart;
            $address = auth()->user()->address()->where('status' , 1)->first();
            if($address){
                if($cart[0]->delivery && $cart[0]->carrier){
                    $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier')->get();
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

                    $Amount = 0;
                    for ( $i = 0; $i < count($count); $i++) {
                        $allSum2 = (int)$count[$i]['price'] * (int)$count[$i]['count'];
                        $Amount = $Amount + (int)$allSum2;
                        if($count[$i]->discount){
                            $discount = Discount::where('code' , $count[$i]->discount)->where('post_id', '!=' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if ($discount) {
                                if($count[$i]['post_id'] == $discount['post_id']){
                                    $Amount = $Amount - (($Amount * $discount->percent) / 100);
                                }
                            }
                        }
                    }

                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $Amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $Amount = (int)$Amount + (int)$sends;
                    if($count[0]->discount){
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $Amount = ($Amount * $discount->percent) / 100;
                        }
                    }
                    $Authority =$request->trackId;
                    $v = verta();
                    $name = auth()->user()->name;
                    if ($request->success == 1) {
                        $client = new Client();
                        $response = $client->request('POST', 'https://gateway.zibal.ir/v1/verify',
                            [
                                'form_params' => [
                                    'merchant' => $MerchantID,
                                    'amount' => $Amount,
                                    'trackId' => $Authority,
                                ],
                                'allow_redirects' => true
                            ]);

                        $contents = $response->getBody()->getContents();
                        $contents = json_decode($contents,true);

                        if ($contents['status'] == 100) {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>$contents['refNumber'],
                                'status'=> 100,
                                'property'=>$chars2,
                                'time' => $count[0]->delivery,
                                'price'=>$Amount,
                                'discount_id'=>$discountId,
                                'user_id'=>auth()->user()->id,
                                'auth'=>$Authority,
                            ]);
                            $pay->address()->attach($address->id);
                            $pay->carrier()->sync($cart[0]['carrier'][0]['id']);
                            for ( $i = 0; $i < count($count); $i++) {
                                $count2 = Post::where('id' , $count[$i]->post_id)->pluck('count')->first();
                                Post::where('id' , $count[$i]->post_id)->first()->update([
                                    'count' => $count2 - $count[$i]->count
                                ]);
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $discount->update([
                                        'count'=> --$discount->count
                                    ]);
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'pay_id' => $pay->id,
                                    'discount_id'=>$discountId,
                                    'status'=>100,
                                    'price'=> $getPrice,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);

                                $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                                if ($count[$i]['color'] != '[]'){
                                    $cartColor = json_decode($count[$i]['color'],true)['name'];
                                    $colors = [];
                                    foreach (json_decode($post['review'][0]['colors'] , true) as $item) {
                                        if($item['name'] == $cartColor){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($colors , $item);
                                    }
                                    $post->review()->first()->update([
                                        'colors' => json_encode($colors),
                                    ]);
                                }
                                if ($count[$i]['size'] != '[]'){
                                    $cartSize = json_decode($count[$i]['size'],true)['name'];
                                    $sizes = [];
                                    foreach (json_decode($post['review'][0]['size'] , true) as $item) {
                                        if($item['name'] == $cartSize){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($sizes , $item);
                                    }
                                    $post->review()->first()->update([
                                        'size' => json_encode($sizes),
                                    ]);
                                }
                                $score = 0;
                                for ( $i = 0; $i < count($count); $i++) {
                                    $allSum2 = (int)$post['score'] * (int)$count[$i]['count'];
                                    $score = $score + (int)$allSum2;
                                }
                            }
                            auth()->user()->cart()->delete();
                            $address = Setting::where('key' , 'address')->pluck('value')->first();
                            $link = $address."show-pay/$pay->property";
                            if(auth()->user()->email){
                                $text2 = "<strong>سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به آدرس زیر برین : </strong><br/> <a href='$link'>پیگیری پرداختی</a>";
                                $this->sendEmail(auth()->user()->email , $text2 , 'خرید موفقیت آمیز');
                            }
                            if(auth()->user()->number){
                                $text2 = "سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به پنل کاربریتان مراجعه کنید";
                                $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                                if($sms == 1){
                                    $username = '';
                                    $password = '';
                                    $api = new MelipayamakApi($username,$password);
                                    $sms = $api->sms();
                                    $to = auth()->user()->number;
                                    $from = '';
                                    $text = $text2;
                                    $response = $sms->send($to,$from,$text);
                                }else{
                                    $this->sendSms(auth()->user()->number , $text2,env('GHASEDAKAPI_Number'));
                                }
                            }
                            if($score >= 1){
                                Score::create([
                                    'name'=>$score,
                                    'user_id'=>auth()->user()->id,
                                ]);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }else {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>'',
                                'status'=>$contents['status'],
                                'property'=>$chars2,
                                'time' => $count[0]->delivery,
                                'discount_id'=>$discountId,
                                'user_id'=>auth()->user()->id,
                                'price'=>$Amount,
                                'auth' => $Authority,
                            ]);
                            $pay->address()->attach($address->id);
                            for ( $i = 0; $i < count($count); $i++) {
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'status'=>$contents['status'],
                                    'pay_id' => $pay->id,
                                    'price'=> $getPrice,
                                    'discount_id'=>$discountId,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }
                    }
                    else{
                        $chars = '012345678901234567890123456789';
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Pay::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                            }
                        }
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $discountId = $discount->id;
                            $discount->update([
                                'count'=> --$discount->count
                            ]);
                        }else{
                            $discountId = null;
                        }
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>0,
                            'price'=>$Amount,
                            'property'=>$chars2,
                            'discount_id'=>$discountId,
                            'time' => $count[0]->delivery,
                            'user_id'=>auth()->user()->id,
                            'auth' => $Authority,
                        ]);
                        $pay->address()->attach($address->id);
                        for ( $i = 0; $i < count($count); $i++) {
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                            }else{
                                $discountId = null;
                                $getPrice = $count[$i]->price * $count[$i]->count;
                            }
                            $payMeta = PayMeta::create([
                                'post_id' => $count[$i]->post_id,
                                'user_id' => $count[$i]->user_id,
                                'status'=>0,
                                'pay_id' => $pay->id,
                                'price'=> $getPrice,
                                'discount_id'=>$discountId,
                                'count' => $count[$i]->count,
                                'color' => $count[$i]->color,
                                'size' => $count[$i]->size,
                            ]);
                            $payMeta->address()->attach($address->id);
                            $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                        }
                        return Inertia::render('Cart/BuyIndex' , [
                            'name' => $name,
                            'pay' => $pay,
                        ]);
                    }
                } else {
                    return redirect('/user-info-cart');
                }
            }else{
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function idpay(Request $request){
        $time = Carbon::now()->format('Y-m-d h:i');
        $MerchantID = Setting::where('key' , 'idpay')->pluck('value')->first();
        if (auth()->user()->cart()->count() >= 1) {
            $cart = auth()->user()->cart;
            $address = auth()->user()->address()->where('status' , 1)->first();
            if($address){
                if($cart[0]->delivery && $cart[0]->carrier){
                    $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier')->get();
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

                    $Amount = 0;
                    for ( $i = 0; $i < count($count); $i++) {
                        $allSum2 = (int)$count[$i]['price'] * (int)$count[$i]['count'];
                        $Amount = $Amount + (int)$allSum2;
                        if($count[$i]->discount){
                            $discount = Discount::where('code' , $count[$i]->discount)->where('post_id', '!=' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if ($discount) {
                                if($count[$i]['post_id'] == $discount['post_id']){
                                    $Amount = $Amount - (($Amount * $discount->percent) / 100);
                                }
                            }
                        }
                    }

                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $Amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $Amount = (int)$Amount + (int)$sends;
                    if($count[0]->discount){
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $Amount = ($Amount * $discount->percent) / 100;
                        }
                    }
                    $Authority =$request->id;
                    $v = verta();
                    $name = auth()->user()->name;
                    if ($request->status == 1) {
                        $params = array(
                            'id' => $request->id,
                            'order_id' => '101',
                        );

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment/verify');
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'X-API-KEY: 6a7f99eb-7c20-4412-a972-6dfb7cd253a4',
                            'X-SANDBOX: 1',
                        ));

                        $result = curl_exec($ch);
                        curl_close($ch);
                        $contents = json_decode($result,true);

                        if ($contents['status'] == 100) {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>$contents['payment']['track_id'],
                                'status'=> 100,
                                'time' => $count[0]->delivery,
                                'property'=>$chars2,
                                'price'=>$Amount,
                                'discount_id'=>$discountId,
                                'user_id'=>auth()->user()->id,
                                'auth'=>$Authority,
                            ]);
                            $pay->address()->attach($address->id);
                            $pay->carrier()->sync($cart[0]['carrier'][0]['id']);
                            for ( $i = 0; $i < count($count); $i++) {
                                $count2 = Post::where('id' , $count[$i]->post_id)->pluck('count')->first();
                                Post::where('id' , $count[$i]->post_id)->first()->update([
                                    'count' => $count2 - $count[$i]->count
                                ]);
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $discount->update([
                                        'count'=> --$discount->count
                                    ]);
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'pay_id' => $pay->id,
                                    'discount_id' => $discountId,
                                    'status'=>100,
                                    'price'=>$getPrice,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);

                                $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                                if ($count[$i]['color'] != '[]'){
                                    $cartColor = json_decode($count[$i]['color'],true)['name'];
                                    $colors = [];
                                    foreach (json_decode($post['review'][0]['colors'] , true) as $item) {
                                        if($item['name'] == $cartColor){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($colors , $item);
                                    }
                                    $post->review()->first()->update([
                                        'colors' => json_encode($colors),
                                    ]);
                                }
                                if ($count[$i]['size'] != '[]'){
                                    $cartSize = json_decode($count[$i]['size'],true)['name'];
                                    $sizes = [];
                                    foreach (json_decode($post['review'][0]['size'] , true) as $item) {
                                        if($item['name'] == $cartSize){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($sizes , $item);
                                    }
                                    $post->review()->first()->update([
                                        'size' => json_encode($sizes),
                                    ]);
                                }
                                $score = 0;
                                for ( $i = 0; $i < count($count); $i++) {
                                    $allSum2 = (int)$post['score'] * (int)$count[$i]['count'];
                                    $score = $score + (int)$allSum2;
                                }
                            }
                            auth()->user()->cart()->delete();
                            $address = Setting::where('key' , 'address')->pluck('value')->first();
                            $link = $address."show-pay/$pay->property";
                            if(auth()->user()->email){
                                $text2 = "<strong>سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به آدرس زیر برین : </strong><br/> <a href='$link'>پیگیری پرداختی</a>";
                                $this->sendEmail(auth()->user()->email , $text2 , 'خرید موفقیت آمیز');
                            }
                            if(auth()->user()->number){
                                $text2 = "سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به پنل کاربریتان مراجعه کنید";
                                $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                                if($sms == 1){
                                    $username = '';
                                    $password = '';
                                    $api = new MelipayamakApi($username,$password);
                                    $sms = $api->sms();
                                    $to = auth()->user()->number;
                                    $from = '';
                                    $text = $text2;
                                    $response = $sms->send($to,$from,$text);
                                }else{
                                    $this->sendSms(auth()->user()->number , $text2,env('GHASEDAKAPI_Number'));
                                }
                            }
                            if($score >= 1){
                                Score::create([
                                    'name'=>$score,
                                    'user_id'=>auth()->user()->id,
                                ]);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);

                        }else {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>'',
                                'status'=>$contents['status'],
                                'property'=>$chars2,
                                'user_id'=>auth()->user()->id,
                                'discount_id' => $discountId,
                                'time' => $count[0]->delivery,
                                'price'=>$Amount,
                                'auth' => $Authority,
                            ]);
                            for ( $i = 0; $i < count($count); $i++) {
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'status'=>$contents['status'],
                                    'pay_id' => $pay->id,
                                    'price'=> $getPrice,
                                    'discount_id' => $discountId,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }
                    }
                    else{
                        $chars = '012345678901234567890123456789';
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Pay::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                            }
                        }
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $discountId = $discount->id;
                            $discount->update([
                                'count'=> --$discount->count
                            ]);
                        }else{
                            $discountId = null;
                        }
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>0,
                            'price'=>$Amount,
                            'property'=>$chars2,
                            'time' => $count[0]->delivery,
                            'discount_id' => $discountId,
                            'user_id'=>auth()->user()->id,
                            'auth' => $Authority,
                        ]);
                        for ( $i = 0; $i < count($count); $i++) {
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                            }else{
                                $discountId = null;
                                $getPrice = $count[$i]->price * $count[$i]->count;
                            }
                            $payMeta = PayMeta::create([
                                'post_id' => $count[$i]->post_id,
                                'user_id' => $count[$i]->user_id,
                                'status'=>0,
                                'pay_id' => $pay->id,
                                'price'=> $getPrice,
                                'discount_id' => $discountId,
                                'count' => $count[$i]->count,
                                'color' => $count[$i]->color,
                                'size' => $count[$i]->size,
                            ]);
                            $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                        }
                        return Inertia::render('Cart/BuyIndex' , [
                            'name' => $name,
                            'pay' => $pay,
                        ]);
                    }
                }else {
                    return redirect('/user-info-cart');
                }
            }else{
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function chargeOrder(Request $request)
    {
        $MerchantID = Setting::where('key' , 'zarinpal')->pluck('value')->first();
        if (auth()->user()) {
            $Amount = auth()->user()->buy;
            $Authority =$request->get('Authority');
            $v = verta();
            $name = auth()->user()->name;
            if ($request->get('Status') == 'OK') {
                $client = new nusoap_client('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
                $client->soap_defencoding = 'UTF-8';

                $result = $client->call('PaymentVerification', [
                    [
                        'MerchantID'     => $MerchantID,
                        'Authority'      => $Authority,
                        'Amount'         => $Amount,
                    ],
                ]);

                if ($result['Status'] == 100) {
                    $chars = '012345678901234567890123456789';
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                    if (Charge::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Charge::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                        }
                    }
                    $pay = Charge::create([
                        'refId'=>$result['RefID'],
                        'status'=>$result['Status'],
                        'property'=>$chars2,
                        'price'=>$Amount,
                        'type'=>0,
                        'user_id'=>auth()->user()->id,
                    ]);
                    auth()->user()->update([
                        'buy'=> null
                    ]);
                    return Inertia::render('Cart/BuyIndex' , [
                        'name' => $name,
                        'pay' => $pay,
                    ]);
                }else {
                    $chars = '012345678901234567890123456789';
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                    if (Pay::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Pay::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                        }
                    }
                    $pay = Charge::create([
                        'refId'=>'',
                        'status'=>$result['Status'],
                        'property'=>$chars2,
                        'price'=>$Amount,
                        'type'=>0,
                        'user_id'=>auth()->user()->id,
                    ]);
                    auth()->user()->update([
                        'buy'=> null
                    ]);
                    return Inertia::render('Cart/BuyIndex' , [
                        'name' => $name,
                        'pay' => $pay,
                    ]);
                }
            }
            else{
                $chars = '012345678901234567890123456789';
                $chars2 = substr(str_shuffle($chars), 0 , 10);
                if (Pay::where('property' , $chars2)->first()){
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                    if (Pay::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                    }
                }
                $pay = Charge::create([
                    'refId'=>'',
                    'status'=>0,
                    'property'=>$chars2,
                    'price'=>$Amount,
                    'type'=>0,
                    'user_id'=>auth()->user()->id,
                ]);
                auth()->user()->update([
                    'buy'=> null
                ]);
                return Inertia::render('Cart/BuyIndex' , [
                    'name' => $name,
                    'pay' => $pay,
                ]);
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function chargeNextPay(Request $request){
        $time = Carbon::now()->format('Y-m-d h:i');
        $MerchantID = Setting::where('key' , 'nextPay')->pluck('value')->first();
        if (auth()->user()) {
            $Amount = auth()->user()->buy;
            $Authority =$request->trans_id;
            $v = verta();
            $name = auth()->user()->name;
            if ($request->np_status != 'Unsuccessful') {
                $client = new Client();
                $response = $client->request('POST', 'https://nextpay.org/nx/gateway/verify',
                    [
                        'form_params' => [
                            'api_key' => $MerchantID,
                            'amount' => $Amount,
                            'trans_id' => $Authority,
                        ],
                        'allow_redirects' => true
                    ]);

                $contents = $response->getBody()->getContents();
                $contents = json_decode($contents,true);


                if ($contents['code'] == 0) {
                    $chars = '012345678901234567890123456789';
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                    if (Charge::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Charge::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                        }
                    }
                    $pay = Charge::create([
                        'refId'=>$contents['Shaparak_Ref_Id'],
                        'status'=> 100,
                        'property'=>$chars2,
                        'type'=>0,
                        'price'=>$Amount,
                        'user_id'=>auth()->user()->id,
                    ]);
                    auth()->user()->update([
                        'buy'=> null
                    ]);
                    return Inertia::render('Cart/BuyIndex' , [
                        'name' => $name,
                        'pay' => $pay,
                    ]);

                }else {
                    $chars = '012345678901234567890123456789';
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                    if (Charge::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Charge::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                        }
                    }
                    $pay = Charge::create([
                        'refId'=>'',
                        'type'=>0,
                        'status'=>$contents['code'],
                        'property'=>$chars2,
                        'user_id'=>auth()->user()->id,
                        'price'=>$Amount,
                    ]);
                    auth()->user()->update([
                        'buy'=> null
                    ]);
                    return Inertia::render('Cart/BuyIndex' , [
                        'name' => $name,
                        'pay' => $pay,
                    ]);
                }
            }
            else{
                $chars = '012345678901234567890123456789';
                $chars2 = substr(str_shuffle($chars), 0 , 10);
                if (Charge::where('property' , $chars2)->first()){
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                    if (Charge::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                    }
                }
                $pay = Charge::create([
                    'refId'=>'',
                    'status'=>0,
                    'type'=>0,
                    'price'=>$Amount,
                    'property'=>$chars2,
                    'user_id'=>auth()->user()->id,
                ]);
                auth()->user()->update([
                    'buy'=> null
                ]);
                return Inertia::render('Cart/BuyIndex' , [
                    'name' => $name,
                    'pay' => $pay,
                ]);
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function chargeZibal(Request $request){
        $time = Carbon::now()->format('Y-m-d h:i');
        $MerchantID = Setting::where('key' , 'zibal')->pluck('value')->first();
        if (auth()->user()) {
            $Amount = auth()->user()->buy;
            $Authority =$request->trackId;
            $v = verta();
            $name = auth()->user()->name;
            if ($request->success == 1) {
                $client = new Client();
                $response = $client->request('POST', 'https://gateway.zibal.ir/v1/verify',
                    [
                        'form_params' => [
                            'merchant' => $MerchantID,
                            'amount' => $Amount,
                            'trackId' => $Authority,
                        ],
                        'allow_redirects' => true
                    ]);

                $contents = $response->getBody()->getContents();
                $contents = json_decode($contents,true);

                if ($contents['status'] == 100) {
                    $chars = '012345678901234567890123456789';
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                    if (Charge::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Charge::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                        }
                    }
                    $pay = Charge::create([
                        'refId'=>$contents['refNumber'],
                        'status'=> 100,
                        'type'=> 0,
                        'property'=>$chars2,
                        'price'=>$Amount,
                        'user_id'=>auth()->user()->id,
                    ]);
                    auth()->user()->update([
                        'buy'=> null
                    ]);
                    return Inertia::render('Cart/BuyIndex' , [
                        'name' => $name,
                        'pay' => $pay,
                    ]);

                }else {
                    $chars = '012345678901234567890123456789';
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                    if (Charge::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Charge::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                        }
                    }
                    $pay = Charge::create([
                        'refId'=>'',
                        'status'=>$contents['status'],
                        'property'=>$chars2,
                        'user_id'=>auth()->user()->id,
                        'price'=>$Amount,
                        'type'=>0,
                    ]);
                    auth()->user()->update([
                        'buy'=> null
                    ]);
                    return Inertia::render('Cart/BuyIndex' , [
                        'name' => $name,
                        'pay' => $pay,
                    ]);
                }
            }
            else{
                $chars = '012345678901234567890123456789';
                $chars2 = substr(str_shuffle($chars), 0 , 10);
                if (Charge::where('property' , $chars2)->first()){
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                    if (Charge::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                    }
                }
                $pay = Pay::create([
                    'refId'=>'',
                    'status'=>0,
                    'type'=>0,
                    'price'=>$Amount,
                    'property'=>$chars2,
                    'user_id'=>auth()->user()->id,
                ]);
                auth()->user()->update([
                    'buy'=> null
                ]);
                return Inertia::render('Cart/BuyIndex' , [
                    'name' => $name,
                    'pay' => $pay,
                ]);
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function chargeIdpay(Request $request){
        $MerchantID = Setting::where('key' , 'idpay')->pluck('value')->first();
        if (auth()->user()) {
            $Amount = auth()->user()->buy;
            $Authority =$request->id;
            $v = verta();
            $name = auth()->user()->name;
            if ($request->status == 1) {
                $params = array(
                    'id' => $request->id,
                    'order_id' => '101',
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment/verify');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'X-API-KEY: 6a7f99eb-7c20-4412-a972-6dfb7cd253a4',
                    'X-SANDBOX: 1',
                ));

                $result = curl_exec($ch);
                curl_close($ch);
                $contents = json_decode($result,true);

                if ($contents['status'] == 100) {
                    $chars = '012345678901234567890123456789';
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                    if (Charge::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Charge::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                        }
                    }
                    $pay = Charge::create([
                        'refId'=>$contents['payment']['track_id'],
                        'status'=> 100,
                        'type'=> 0,
                        'property'=>$chars2,
                        'price'=>$Amount,
                        'user_id'=>auth()->user()->id,
                    ]);
                    auth()->user()->update([
                        'buy'=> null
                    ]);
                    return Inertia::render('Cart/BuyIndex' , [
                        'name' => $name,
                        'pay' => $pay,
                    ]);

                }else {
                    $chars = '012345678901234567890123456789';
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                    if (Charge::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Charge::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                        }
                    }
                    $pay = Charge::create([
                        'refId'=>'',
                        'type'=>0,
                        'status'=>$contents['status'],
                        'property'=>$chars2,
                        'user_id'=>auth()->user()->id,
                        'price'=>$Amount,
                        'auth' => $Authority,
                    ]);
                    auth()->user()->update([
                        'buy'=> null
                    ]);
                    return Inertia::render('Cart/BuyIndex' , [
                        'name' => $name,
                        'pay' => $pay,
                    ]);
                }
            }
            else{
                $chars = '012345678901234567890123456789';
                $chars2 = substr(str_shuffle($chars), 0 , 10);
                if (Charge::where('property' , $chars2)->first()){
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                    if (Charge::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                    }
                }
                $pay = Charge::create([
                    'refId'=>'',
                    'status'=>0,
                    'type'=>0,
                    'price'=>$Amount,
                    'property'=>$chars2,
                    'user_id'=>auth()->user()->id,
                ]);
                auth()->user()->update([
                    'buy'=> null
                ]);
                return Inertia::render('Cart/BuyIndex' , [
                    'name' => $name,
                    'pay' => $pay,
                ]);
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function spotOrder(Request $request)
    {
        $time = Carbon::now()->format('Y-m-d h:i');
        $MerchantID = Setting::where('key' , 'zarinpal')->pluck('value')->first();
        if (auth()->user()->cart()->count() >= 1) {
            $cart = auth()->user()->cart;
            $address = auth()->user()->address()->where('status' , 1)->first();
            if($address){
                if($cart[0]->delivery && $cart[0]->carrier){
                    $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier')->get();
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

                    $Amount = 0;
                    for ( $i = 0; $i < count($count); $i++) {
                        $allSum2 = (int)$count[$i]['price'] * (int)$count[$i]['count'];
                        $Amount = $Amount + (int)$allSum2;
                        if($count[$i]->discount){
                            $discount = Discount::where('code' , $count[$i]->discount)->where('post_id', '!=' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if ($discount) {
                                if($count[$i]['post_id'] == $discount['post_id']){
                                    $Amount = $Amount - (($Amount * $discount->percent) / 100);
                                }
                            }
                        }
                    }

                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $Amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $Amount = (int)$Amount + (int)$sends;
                    if($count[0]->discount){
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $Amount = ($Amount * $discount->percent) / 100;
                        }
                    }
                    $deposit = Setting::where('key' , 'deposit')->pluck('value')->first();
                    $amountA = round($Amount * (int)$deposit / 100);

                    $Authority =$request->get('Authority');
                    $v = verta();
                    $name = auth()->user()->name;
                    if ($request->get('Status') == 'OK') {
                        $client = new nusoap_client('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
                        $client->soap_defencoding = 'UTF-8';

                        $result = $client->call('PaymentVerification', [
                            [
                                'MerchantID'     => $MerchantID,
                                'Authority'      => $Authority,
                                'Amount'         => $amountA,
                            ],
                        ]);

                        if ($result['Status'] == 100) {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>$result['RefID'],
                                'status'=>50,
                                'property'=>$chars2,
                                'time' => $count[0]->delivery,
                                'price'=>$Amount,
                                'deposit'=>$amountA,
                                'method'=>2,
                                'discount_id'=>$discountId,
                                'user_id'=>auth()->user()->id,
                                'auth'=>$Authority,
                            ]);
                            $pay->carrier()->sync($cart[0]['carrier'][0]['id']);
                            $pay->address()->attach($address->id);
                            for ( $i = 0; $i < count($count); $i++) {
                                $count2 = Post::where('id' , $count[$i]->post_id)->pluck('count')->first();
                                Post::where('id' , $count[$i]->post_id)->first()->update([
                                    'count' => $count2 - $count[$i]->count
                                ]);
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $discount->update([
                                        'count'=> --$discount->count
                                    ]);
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'pay_id' => $pay->id,
                                    'discount_id'=>$discountId,
                                    'status'=>50,
                                    'price'=> $getPrice,
                                    'method'=> 2,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);

                                $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                                if ($count[$i]['color'] != '[]'){
                                    $cartColor = json_decode($count[$i]['color'],true)['name'];
                                    $colors = [];
                                    foreach (json_decode($post['review'][0]['colors'] , true) as $item) {
                                        if($item['name'] == $cartColor){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($colors , $item);
                                    }
                                    $post->review()->first()->update([
                                        'colors' => json_encode($colors),
                                    ]);
                                }
                                if ($count[$i]['size'] != '[]'){
                                    $cartSize = json_decode($count[$i]['size'],true)['name'];
                                    $sizes = [];
                                    foreach (json_decode($post['review'][0]['size'] , true) as $item) {
                                        if($item['name'] == $cartSize){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($sizes , $item);
                                    }
                                    $post->review()->first()->update([
                                        'size' => json_encode($sizes),
                                    ]);
                                }
                                $score = 0;
                                for ( $i = 0; $i < count($count); $i++) {
                                    $allSum2 = (int)$post['score'] * (int)$count[$i]['count'];
                                    $score = $score + (int)$allSum2;
                                }
                            }
                            auth()->user()->cart()->delete();
                            $address = Setting::where('key' , 'address')->pluck('value')->first();
                            $link = $address."show-pay/$pay->property";
                            if(auth()->user()->email){
                                $text2 = "<strong>سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به آدرس زیر برین : </strong><br/> <a href='$link'>پیگیری پرداختی</a>";
                                $this->sendEmail(auth()->user()->email , $text2 , 'خرید موفقیت آمیز');
                            }
                            if(auth()->user()->number){
                                $text2 = "سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به پنل کاربریتان مراجعه کنید";
                                $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                                if($sms == 1){
                                    $username = '';
                                    $password = '';
                                    $api = new MelipayamakApi($username,$password);
                                    $sms = $api->sms();
                                    $to = auth()->user()->number;
                                    $from = '';
                                    $text = $text2;
                                    $response = $sms->send($to,$from,$text);
                                }else{
                                    $this->sendSms(auth()->user()->number , $text2,env('GHASEDAKAPI_Number'));
                                }
                            }
                            if($score >= 1){
                                Score::create([
                                    'name'=>$score,
                                    'user_id'=>auth()->user()->id,
                                ]);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }else {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>'',
                                'status'=>$result['Status'],
                                'property'=>$chars2,
                                'time' => $count[0]->delivery,
                                'user_id'=>auth()->user()->id,
                                'discount'=> $discountId,
                                'price'=>$Amount,
                                'deposit'=>$amountA,
                                'method'=>2,
                                'auth' => $request->Authority,
                            ]);
                            $pay->address()->attach($address->id);
                            for ( $i = 0; $i < count($count); $i++) {
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'status'=> $result['Status'],
                                    'discount'=> $discountId,
                                    'pay_id' => $pay->id,
                                    'price'=> $getPrice,
                                    'method'=>2,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }
                    }
                    else{
                        $chars = '012345678901234567890123456789';
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Pay::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                            }
                        }
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $discountId = $discount->id;
                        }else{
                            $discountId = null;
                        }
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>0,
                            'price'=>$Amount,
                            'time' => $count[0]->delivery,
                            'property'=>$chars2,
                            'deposit'=>$amountA,
                            'method'=>2,
                            'discount_id'=> $discountId,
                            'user_id'=>auth()->user()->id,
                            'auth' => $request->Authority,
                        ]);
                        $pay->address()->attach($address->id);
                        for ( $i = 0; $i < count($count); $i++) {
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                            }else{
                                $discountId = null;
                                $getPrice = $count[$i]->price * $count[$i]->count;
                            }
                            $payMeta = PayMeta::create([
                                'post_id' => $count[$i]->post_id,
                                'user_id' => $count[$i]->user_id,
                                'status'=>0,
                                'pay_id' => $pay->id,
                                'price'=> $getPrice,
                                'method'=>2,
                                'discount_id'=> $discountId,
                                'count' => $count[$i]->count,
                                'color' => $count[$i]->color,
                                'size' => $count[$i]->size,
                            ]);
                            $payMeta->address()->attach($address->id);
                            $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                        }
                        return Inertia::render('Cart/BuyIndex' , [
                            'name' => $name,
                            'pay' => $pay,
                        ]);
                    }
                }else {
                    return redirect('/user-info-cart');
                }
            }else{
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function spotNextPay(Request $request){
        $time = Carbon::now()->format('Y-m-d h:i');
        $MerchantID = Setting::where('key' , 'nextPay')->pluck('value')->first();
        if (auth()->user()->cart()->count() >= 1) {
            $cart = auth()->user()->cart;
            $address = auth()->user()->address()->where('status' , 1)->first();
            if($address){
                if($cart[0]->delivery && $cart[0]->carrier){
                    $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier')->get();
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

                    $Amount = 0;
                    for ( $i = 0; $i < count($count); $i++) {
                        $allSum2 = (int)$count[$i]['price'] * (int)$count[$i]['count'];
                        $Amount = $Amount + (int)$allSum2;
                        if($count[$i]->discount){
                            $discount = Discount::where('code' , $count[$i]->discount)->where('post_id', '!=' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if ($discount) {
                                if($count[$i]['post_id'] == $discount['post_id']){
                                    $Amount = $Amount - (($Amount * $discount->percent) / 100);
                                }
                            }
                        }
                    }

                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $Amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $Amount = (int)$Amount + (int)$sends;
                    if($count[0]->discount){
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $Amount = ($Amount * $discount->percent) / 100;
                        }
                    }
                    $deposit = Setting::where('key' , 'deposit')->pluck('value')->first();
                    $amountA = round($Amount * (int)$deposit / 100);

                    $Authority =$request->trans_id;
                    $v = verta();
                    $name = auth()->user()->name;
                    if ($request->np_status != 'Unsuccessful') {
                        $client = new Client();
                        $response = $client->request('POST', 'https://nextpay.org/nx/gateway/verify',
                            [
                                'form_params' => [
                                    'api_key' => $MerchantID,
                                    'amount' => $amountA,
                                    'trans_id' => $Authority,
                                ],
                                'allow_redirects' => true
                            ]);

                        $contents = $response->getBody()->getContents();
                        $contents = json_decode($contents,true);


                        if ($contents['code'] == 0) {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>$contents['Shaparak_Ref_Id'],
                                'status'=> 50,
                                'property'=>$chars2,
                                'time' => $count[0]->delivery,
                                'price'=>$Amount,
                                'deposit'=>$amountA,
                                'method'=>2,
                                'discount_id'=>$discountId,
                                'user_id'=>auth()->user()->id,
                                'auth'=>$Authority,
                            ]);
                            $pay->address()->attach($address->id);
                            $pay->carrier()->sync($cart[0]['carrier'][0]['id']);
                            for ( $i = 0; $i < count($count); $i++) {
                                $count2 = Post::where('id' , $count[$i]->post_id)->pluck('count')->first();
                                Post::where('id' , $count[$i]->post_id)->first()->update([
                                    'count' => $count2 - $count[$i]->count
                                ]);
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $discount->update([
                                        'count'=> --$discount->count
                                    ]);
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'pay_id' => $pay->id,
                                    'discount_id' => $discountId,
                                    'status'=>50,
                                    'price'=> $getPrice,
                                    'method'=>2,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);

                                $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                                if ($count[$i]['color'] != '[]'){
                                    $cartColor = json_decode($count[$i]['color'],true)['name'];
                                    $colors = [];
                                    foreach (json_decode($post['review'][0]['colors'] , true) as $item) {
                                        if($item['name'] == $cartColor){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($colors , $item);
                                    }
                                    $post->review()->first()->update([
                                        'colors' => json_encode($colors),
                                    ]);
                                }
                                if ($count[$i]['size'] != '[]'){
                                    $cartSize = json_decode($count[$i]['size'],true)['name'];
                                    $sizes = [];
                                    foreach (json_decode($post['review'][0]['size'] , true) as $item) {
                                        if($item['name'] == $cartSize){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($sizes , $item);
                                    }
                                    $post->review()->first()->update([
                                        'size' => json_encode($sizes),
                                    ]);
                                }
                                $score = 0;
                                for ( $i = 0; $i < count($count); $i++) {
                                    $allSum2 = (int)$post['score'] * (int)$count[$i]['count'];
                                    $score = $score + (int)$allSum2;
                                }
                            }
                            auth()->user()->cart()->delete();
                            $address = Setting::where('key' , 'address')->pluck('value')->first();
                            $link = $address."show-pay/$pay->property";
                            if(auth()->user()->email){
                                $text2 = "<strong>سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به آدرس زیر برین : </strong><br/> <a href='$link'>پیگیری پرداختی</a>";
                                $this->sendEmail(auth()->user()->email , $text2 , 'خرید موفقیت آمیز');
                            }
                            if(auth()->user()->number){
                                $text2 = "سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به پنل کاربریتان مراجعه کنید";
                                $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                                if($sms == 1){
                                    $username = '';
                                    $password = '';
                                    $api = new MelipayamakApi($username,$password);
                                    $sms = $api->sms();
                                    $to = auth()->user()->number;
                                    $from = '';
                                    $text = $text2;
                                    $response = $sms->send($to,$from,$text);
                                }else{
                                    $this->sendSms(auth()->user()->number , $text2,env('GHASEDAKAPI_Number'));
                                }
                            }
                            if($score >= 1){
                                Score::create([
                                    'name'=>$score,
                                    'user_id'=>auth()->user()->id,
                                ]);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }else {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>'',
                                'status'=>0,
                                'property'=>$chars2,
                                'time' => $count[0]->delivery,
                                'user_id'=>auth()->user()->id,
                                'discount_id' => $discountId,
                                'price'=>$Amount,
                                'deposit'=>$amountA,
                                'method'=>2,
                                'auth' => $Authority,
                            ]);
                            $pay->address()->attach($address->id);
                            for ( $i = 0; $i < count($count); $i++) {
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'status'=>$contents['code'],
                                    'pay_id' => $pay->id,
                                    'discount_id' => $discountId,
                                    'price'=> $getPrice,
                                    'method'=>2,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }
                    }
                    else{
                        $chars = '012345678901234567890123456789';
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Pay::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                            }
                        }
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $discountId = $discount->id;
                        }else{
                            $discountId = null;
                        }
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>0,
                            'time' => $count[0]->delivery,
                            'price'=>$Amount,
                            'property'=>$chars2,
                            'deposit'=>$amountA,
                            'method'=>2,
                            'discount_id' => $discountId,
                            'user_id'=>auth()->user()->id,
                            'auth' => $Authority,
                        ]);
                        $pay->address()->attach($address->id);
                        for ( $i = 0; $i < count($count); $i++) {
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                            }else{
                                $discountId = null;
                                $getPrice = $count[$i]->price * $count[$i]->count;
                            }
                            $payMeta = PayMeta::create([
                                'post_id' => $count[$i]->post_id,
                                'user_id' => $count[$i]->user_id,
                                'status'=>0,
                                'pay_id' => $pay->id,
                                'discount_id' => $discountId,
                                'method'=>2,
                                'price'=> $getPrice,
                                'count' => $count[$i]->count,
                                'color' => $count[$i]->color,
                                'size' => $count[$i]->size,
                            ]);
                            $payMeta->address()->attach($address->id);
                            $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                        }
                        return Inertia::render('Cart/BuyIndex' , [
                            'name' => $name,
                            'pay' => $pay,
                        ]);
                    }
                }else {
                    return redirect('/user-info-cart');
                }
            }else{
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function spotZibal(Request $request){
        $time = Carbon::now()->format('Y-m-d h:i');
        $MerchantID = Setting::where('key' , 'zibal')->pluck('value')->first();
        if (auth()->user()->cart()->count() >= 1) {
            $cart = auth()->user()->cart;
            $address = auth()->user()->address()->where('status' , 1)->first();
            if($address){
                if($cart[0]->delivery && $cart[0]->carrier){
                    $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier')->get();
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

                    $Amount = 0;
                    for ( $i = 0; $i < count($count); $i++) {
                        $allSum2 = (int)$count[$i]['price'] * (int)$count[$i]['count'];
                        $Amount = $Amount + (int)$allSum2;
                        if($count[$i]->discount){
                            $discount = Discount::where('code' , $count[$i]->discount)->where('post_id', '!=' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if ($discount) {
                                if($count[$i]['post_id'] == $discount['post_id']){
                                    $Amount = $Amount - (($Amount * $discount->percent) / 100);
                                }
                            }
                        }
                    }

                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $Amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $Amount = (int)$Amount + (int)$sends;
                    if($count[0]->discount){
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $Amount = ($Amount * $discount->percent) / 100;
                        }
                    }
                    $deposit = Setting::where('key' , 'deposit')->pluck('value')->first();
                    $amountA = round($Amount * (int)$deposit / 100);

                    $Authority =$request->trackId;
                    $v = verta();
                    $name = auth()->user()->name;
                    if ($request->success == 1) {
                        $client = new Client();
                        $response = $client->request('POST', 'https://gateway.zibal.ir/v1/verify',
                            [
                                'form_params' => [
                                    'merchant' => $MerchantID,
                                    'amount' => $amountA,
                                    'trackId' => $Authority,
                                ],
                                'allow_redirects' => true
                            ]);

                        $contents = $response->getBody()->getContents();
                        $contents = json_decode($contents,true);

                        if ($contents['status'] == 100) {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>$contents['refNumber'],
                                'status'=> 50,
                                'property'=>$chars2,
                                'time' => $count[0]->delivery,
                                'price'=>$Amount,
                                'deposit'=>$amountA,
                                'method'=>2,
                                'discount_id'=>$discountId,
                                'user_id'=>auth()->user()->id,
                                'auth'=>$Authority,
                            ]);
                            $pay->address()->attach($address->id);
                            $pay->carrier()->sync($cart[0]['carrier'][0]['id']);
                            for ( $i = 0; $i < count($count); $i++) {
                                $count2 = Post::where('id' , $count[$i]->post_id)->pluck('count')->first();
                                Post::where('id' , $count[$i]->post_id)->first()->update([
                                    'count' => $count2 - $count[$i]->count
                                ]);
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $discount->update([
                                        'count'=> --$discount->count
                                    ]);
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'pay_id' => $pay->id,
                                    'discount_id'=>$discountId,
                                    'status'=>50,
                                    'method'=>2,
                                    'price'=> $getPrice,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);

                                $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                                if ($count[$i]['color'] != '[]'){
                                    $cartColor = json_decode($count[$i]['color'],true)['name'];
                                    $colors = [];
                                    foreach (json_decode($post['review'][0]['colors'] , true) as $item) {
                                        if($item['name'] == $cartColor){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($colors , $item);
                                    }
                                    $post->review()->first()->update([
                                        'colors' => json_encode($colors),
                                    ]);
                                }
                                if ($count[$i]['size'] != '[]'){
                                    $cartSize = json_decode($count[$i]['size'],true)['name'];
                                    $sizes = [];
                                    foreach (json_decode($post['review'][0]['size'] , true) as $item) {
                                        if($item['name'] == $cartSize){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($sizes , $item);
                                    }
                                    $post->review()->first()->update([
                                        'size' => json_encode($sizes),
                                    ]);
                                }
                                $score = 0;
                                for ( $i = 0; $i < count($count); $i++) {
                                    $allSum2 = (int)$post['score'] * (int)$count[$i]['count'];
                                    $score = $score + (int)$allSum2;
                                }
                            }
                            auth()->user()->cart()->delete();
                            $address = Setting::where('key' , 'address')->pluck('value')->first();
                            $link = $address."show-pay/$pay->property";
                            if(auth()->user()->email){
                                $text2 = "<strong>سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به آدرس زیر برین : </strong><br/> <a href='$link'>پیگیری پرداختی</a>";
                                $this->sendEmail(auth()->user()->email , $text2 , 'خرید موفقیت آمیز');
                            }
                            if(auth()->user()->number){
                                $text2 = "سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به پنل کاربریتان مراجعه کنید";
                                $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                                if($sms == 1){
                                    $username = '';
                                    $password = '';
                                    $api = new MelipayamakApi($username,$password);
                                    $sms = $api->sms();
                                    $to = auth()->user()->number;
                                    $from = '';
                                    $text = $text2;
                                    $response = $sms->send($to,$from,$text);
                                }else{
                                    $this->sendSms(auth()->user()->number , $text2,env('GHASEDAKAPI_Number'));
                                }
                            }
                            if($score >= 1){
                                Score::create([
                                    'name'=>$score,
                                    'user_id'=>auth()->user()->id,
                                ]);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }else {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>'',
                                'status'=>$contents['status'],
                                'property'=>$chars2,
                                'time' => $count[0]->delivery,
                                'discount_id'=>$discountId,
                                'user_id'=>auth()->user()->id,
                                'price'=>$Amount,
                                'deposit'=>$amountA,
                                'method'=>2,
                                'auth' => $Authority,
                            ]);
                            $pay->address()->attach($address->id);
                            for ( $i = 0; $i < count($count); $i++) {
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'status'=>$contents['status'],
                                    'pay_id' => $pay->id,
                                    'price'=> $getPrice,
                                    'discount_id'=>$discountId,
                                    'count' => $count[$i]->count,
                                    'method'=>2,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }
                    }
                    else{
                        $chars = '012345678901234567890123456789';
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Pay::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                            }
                        }
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $discountId = $discount->id;
                            $discount->update([
                                'count'=> --$discount->count
                            ]);
                        }else{
                            $discountId = null;
                        }
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>0,
                            'price'=>$Amount,
                            'property'=>$chars2,
                            'discount_id'=>$discountId,
                            'deposit'=>$amountA,
                            'method'=>2,
                            'time' => $count[0]->delivery,
                            'user_id'=>auth()->user()->id,
                            'auth' => $Authority,
                        ]);
                        $pay->address()->attach($address->id);
                        for ( $i = 0; $i < count($count); $i++) {
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                            }else{
                                $discountId = null;
                                $getPrice = $count[$i]->price * $count[$i]->count;
                            }
                            $payMeta = PayMeta::create([
                                'post_id' => $count[$i]->post_id,
                                'user_id' => $count[$i]->user_id,
                                'status'=>0,
                                'pay_id' => $pay->id,
                                'price'=> $getPrice,
                                'method'=>2,
                                'discount_id'=>$discountId,
                                'count' => $count[$i]->count,
                                'color' => $count[$i]->color,
                                'size' => $count[$i]->size,
                            ]);
                            $payMeta->address()->attach($address->id);
                            $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                        }
                        return Inertia::render('Cart/BuyIndex' , [
                            'name' => $name,
                            'pay' => $pay,
                        ]);
                    }
                } else {
                    return redirect('/user-info-cart');
                }
            }else{
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function spotIdpay(Request $request){
        $time = Carbon::now()->format('Y-m-d h:i');
        $MerchantID = Setting::where('key' , 'idpay')->pluck('value')->first();
        if (auth()->user()->cart()->count() >= 1) {
            $cart = auth()->user()->cart;
            $address = auth()->user()->address()->where('status' , 1)->first();
            if($address){
                if($cart[0]->delivery && $cart[0]->carrier){
                    $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier')->get();
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

                    $Amount = 0;
                    for ( $i = 0; $i < count($count); $i++) {
                        $allSum2 = (int)$count[$i]['price'] * (int)$count[$i]['count'];
                        $Amount = $Amount + (int)$allSum2;
                        if($count[$i]->discount){
                            $discount = Discount::where('code' , $count[$i]->discount)->where('post_id', '!=' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if ($discount) {
                                if($count[$i]['post_id'] == $discount['post_id']){
                                    $Amount = $Amount - (($Amount * $discount->percent) / 100);
                                }
                            }
                        }
                    }

                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $Amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $Amount = (int)$Amount + (int)$sends;
                    if($count[0]->discount){
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $Amount = ($Amount * $discount->percent) / 100;
                        }
                    }

                    $deposit = Setting::where('key' , 'deposit')->pluck('value')->first();
                    $amountA = round($Amount * (int)$deposit / 100);

                    $Authority =$request->id;
                    $v = verta();
                    $name = auth()->user()->name;
                    if ($request->status == 1) {
                        $params = array(
                            'id' => $request->id,
                            'order_id' => '101',
                        );

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment/verify');
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'X-API-KEY: 6a7f99eb-7c20-4412-a972-6dfb7cd253a4',
                            'X-SANDBOX: 1',
                        ));

                        $result = curl_exec($ch);
                        curl_close($ch);
                        $contents = json_decode($result,true);

                        if ($contents['status'] == 100) {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>$contents['payment']['track_id'],
                                'status'=> 50,
                                'time' => $count[0]->delivery,
                                'property'=>$chars2,
                                'price'=>$Amount,
                                'deposit'=>$amountA,
                                'method'=>2,
                                'discount_id'=>$discountId,
                                'user_id'=>auth()->user()->id,
                                'auth'=>$Authority,
                            ]);
                            $pay->address()->attach($address->id);
                            $pay->carrier()->sync($cart[0]['carrier'][0]['id']);
                            for ( $i = 0; $i < count($count); $i++) {
                                $count2 = Post::where('id' , $count[$i]->post_id)->pluck('count')->first();
                                Post::where('id' , $count[$i]->post_id)->first()->update([
                                    'count' => $count2 - $count[$i]->count
                                ]);
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $discount->update([
                                        'count'=> --$discount->count
                                    ]);
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'pay_id' => $pay->id,
                                    'discount_id' => $discountId,
                                    'status'=>50,
                                    'method'=>2,
                                    'price'=>$getPrice,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->address()->attach($address->id);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);

                                $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                                if ($count[$i]['color'] != '[]'){
                                    $cartColor = json_decode($count[$i]['color'],true)['name'];
                                    $colors = [];
                                    foreach (json_decode($post['review'][0]['colors'] , true) as $item) {
                                        if($item['name'] == $cartColor){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($colors , $item);
                                    }
                                    $post->review()->first()->update([
                                        'colors' => json_encode($colors),
                                    ]);
                                }
                                if ($count[$i]['size'] != '[]'){
                                    $cartSize = json_decode($count[$i]['size'],true)['name'];
                                    $sizes = [];
                                    foreach (json_decode($post['review'][0]['size'] , true) as $item) {
                                        if($item['name'] == $cartSize){
                                            $item['count'] = (int)$item['count'] - (int)$count[$i]['count'];
                                        }
                                        array_push($sizes , $item);
                                    }
                                    $post->review()->first()->update([
                                        'size' => json_encode($sizes),
                                    ]);
                                }
                                $score = 0;
                                for ( $i = 0; $i < count($count); $i++) {
                                    $allSum2 = (int)$post['score'] * (int)$count[$i]['count'];
                                    $score = $score + (int)$allSum2;
                                }
                            }
                            auth()->user()->cart()->delete();
                            $address = Setting::where('key' , 'address')->pluck('value')->first();
                            $link = $address."show-pay/$pay->property";
                            if(auth()->user()->email){
                                $text2 = "<strong>سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به آدرس زیر برین : </strong><br/> <a href='$link'>پیگیری پرداختی</a>";
                                $this->sendEmail(auth()->user()->email , $text2 , 'خرید موفقیت آمیز');
                            }
                            if(auth()->user()->number){
                                $text2 = "سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به پنل کاربریتان مراجعه کنید";
                                $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                                if($sms == 1){
                                    $username = '';
                                    $password = '';
                                    $api = new MelipayamakApi($username,$password);
                                    $sms = $api->sms();
                                    $to = auth()->user()->number;
                                    $from = '';
                                    $text = $text2;
                                    $response = $sms->send($to,$from,$text);
                                }else{
                                    $this->sendSms(auth()->user()->number , $text2,env('GHASEDAKAPI_Number'));
                                }
                            }
                            if($score >= 1){
                                Score::create([
                                    'name'=>$score,
                                    'user_id'=>auth()->user()->id,
                                ]);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);

                        }else {
                            $chars = '012345678901234567890123456789';
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                                if (Pay::where('property' , $chars2)->first()){
                                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                                }
                            }
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $discount->update([
                                    'count'=> --$discount->count
                                ]);
                            }else{
                                $discountId = null;
                            }
                            $pay = Pay::create([
                                'refId'=>'',
                                'status'=>$contents['status'],
                                'property'=>$chars2,
                                'user_id'=>auth()->user()->id,
                                'discount_id' => $discountId,
                                'time' => $count[0]->delivery,
                                'price'=>$Amount,
                                'deposit'=>$amountA,
                                'method'=>2,
                                'auth' => $Authority,
                            ]);
                            for ( $i = 0; $i < count($count); $i++) {
                                $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                                if($discount){
                                    $discountId = $discount->id;
                                    $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                                }else{
                                    $discountId = null;
                                    $getPrice = $count[$i]->price * $count[$i]->count;
                                }
                                $payMeta = PayMeta::create([
                                    'post_id' => $count[$i]->post_id,
                                    'user_id' => $count[$i]->user_id,
                                    'status'=>$contents['status'],
                                    'pay_id' => $pay->id,
                                    'price'=> $getPrice,
                                    'method'=>2,
                                    'discount_id' => $discountId,
                                    'count' => $count[$i]->count,
                                    'color' => $count[$i]->color,
                                    'size' => $count[$i]->size,
                                ]);
                                $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                            }
                            return Inertia::render('Cart/BuyIndex' , [
                                'name' => $name,
                                'pay' => $pay,
                            ]);
                        }
                    }
                    else{
                        $chars = '012345678901234567890123456789';
                        $chars2 = substr(str_shuffle($chars), 0 , 10);
                        if (Pay::where('property' , $chars2)->first()){
                            $chars2 = substr(str_shuffle($chars), 0 , 10);
                            if (Pay::where('property' , $chars2)->first()){
                                $chars2 = substr(str_shuffle($chars), 0 , 10);
                            }
                        }
                        $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , null)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                        if($discount){
                            $discountId = $discount->id;
                            $discount->update([
                                'count'=> --$discount->count
                            ]);
                        }else{
                            $discountId = null;
                        }
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>0,
                            'price'=>$Amount,
                            'property'=>$chars2,
                            'time' => $count[0]->delivery,
                            'discount_id' => $discountId,
                            'deposit'=>$amountA,
                            'method'=>2,
                            'user_id'=>auth()->user()->id,
                            'auth' => $Authority,
                        ]);
                        for ( $i = 0; $i < count($count); $i++) {
                            $discount = Discount::where('code' , $count[0]->discount)->where('post_id' , $count[$i]->post_id)->where('status' , 1)->where('day', '>=' , $time)->where('count' , '>=' , 1)->first();
                            if($discount){
                                $discountId = $discount->id;
                                $getPrice = ( $count[$i]->price - (($count[$i]['price'] * $discount->percent) / 100)) * $count[$i]->count;
                            }else{
                                $discountId = null;
                                $getPrice = $count[$i]->price * $count[$i]->count;
                            }
                            $payMeta = PayMeta::create([
                                'post_id' => $count[$i]->post_id,
                                'user_id' => $count[$i]->user_id,
                                'status'=>0,
                                'pay_id' => $pay->id,
                                'price'=> $getPrice,
                                'discount_id' => $discountId,
                                'method'=>2,
                                'count' => $count[$i]->count,
                                'color' => $count[$i]->color,
                                'size' => $count[$i]->size,
                            ]);
                            $payMeta->guarantee()->sync($count[$i]->guarantee_id);
                        }
                        return Inertia::render('Cart/BuyIndex' , [
                            'name' => $name,
                            'pay' => $pay,
                        ]);
                    }
                }else {
                    return redirect('/user-info-cart');
                }
            }else{
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }
}
