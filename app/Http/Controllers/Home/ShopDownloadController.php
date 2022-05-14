<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\lib\idpay;
use App\lib\nextpay;
use App\lib\zarinpal;
use App\lib\zibal;
use App\Models\Pay;
use App\Models\PayMeta;
use App\Models\Post;
use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use nusoap_client;

class ShopDownloadController extends Controller
{
    public function add_order(Request $request)
    {
        $choicePay = Setting::where('key' , 'choicePay')->pluck('value')->first();
        if (auth()->user()->userMeta()->count() >= 1) {
            $number = auth()->user()->pluck('number')->first();
            auth()->user()->update([
                'buy'=> $request->product
            ]);
            $post = Post::where('id' , $request->product)->where('type' , 1)->first();
            if($post){
                $amount = (int)$post->price;
                if($choicePay == 0){
                    $order = new zarinpal();
                    $res = $order->pay($amount,auth()->user()->email,$number , '/download/order');
                    return redirect('https://sandbox.zarinpal.com/pg/StartPay/' . $res);
                }
                if($choicePay == 1){
                    $order = new zibal();
                    $res = $order->pay($amount,auth()->user()->email,$number , '/download/order/zibal');
                    return redirect('https://gateway.zibal.ir/start/' . $res);
                }
                if($choicePay == 2){
                    $order = new nextpay();
                    $res = $order->pay($amount,auth()->user()->email,$number , '/download/order/nextPay');
                    return redirect("https://nextpay.org/nx/gateway/payment/".$res);
                }
                if($choicePay == 3){
                    $order = new idpay();
                    $res = $order->pay($amount,auth()->user()->email,$number , '/download/order/idpay');
                    return redirect("https://idpay.ir/p/ws-sandbox/".$res);
                }
            }else{
                return abort(404);
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function order(Request $request)
    {
        $MerchantID = Setting::where('key' , 'zarinpal')->pluck('value')->first();
        if (auth()->user()->userMeta()->count()) {
            $post = Post::where('id',auth()->user()->buy)->where('type' , 1)->first();
            if($post){
                $Amount = (int)$post->price;
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
                        $pay = Pay::create([
                            'refId'=>$result['RefID'],
                            'status'=>$result['Status'],
                            'deliver'=>4,
                            'property'=>$chars2,
                            'price'=>$Amount,
                            'user_id'=>auth()->user()->id,
                            'auth'=>$Authority,
                        ]);
                        $payMeta = PayMeta::create([
                            'post_id' => $post->id,
                            'user_id' => auth()->user()->id,
                            'pay_id' => $pay->id,
                            'status'=>$result['Status'],
                            'price'=> $post->price,
                            'count' => 1,
                            'color' => '[]',
                            'size' => '[]',
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
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>$result['Status'],
                            'property'=>$chars2,
                            'user_id'=>auth()->user()->id,
                            'price'=>$Amount,
                            'auth' => $request->Authority,
                        ]);
                        PayMeta::create([
                            'post_id' => $post->id,
                            'user_id' => auth()->user()->id,
                            'pay_id' => $pay->id,
                            'status'=>$result['Status'],
                            'price'=> $post->price,
                            'count' => 1,
                            'color' => '[]',
                            'size' => '[]',
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
                    $pay = Pay::create([
                        'refId'=>'',
                        'status'=>0,
                        'price'=>$Amount,
                        'property'=>$chars2,
                        'user_id'=>auth()->user()->id,
                        'auth' => $request->Authority,
                    ]);
                    PayMeta::create([
                        'post_id' => $post->id,
                        'user_id' => auth()->user()->id,
                        'pay_id' => $pay->id,
                        'status'=>0,
                        'price'=> $post->price,
                        'count' => 1,
                        'color' => '[]',
                        'size' => '[]',
                    ]);
                    auth()->user()->update([
                        'buy'=> null
                    ]);
                    return Inertia::render('Cart/BuyIndex' , [
                        'name' => $name,
                        'pay' => $pay,
                    ]);
                }
            }else {
                return abort(404);
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function nextPay(Request $request){
        $MerchantID = Setting::where('key' , 'nextPay')->pluck('value')->first();
        if (auth()->user()->userMeta()->count() >= 1) {
            $post = Post::where('id',auth()->user()->buy)->where('type' , 1)->first();
            if($post){
                $Amount = (int)$post->price;
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
                        $pay = Pay::create([
                            'refId'=>$contents['Shaparak_Ref_Id'],
                            'status'=> 100,
                            'deliver'=> 4,
                            'property'=>$chars2,
                            'price'=>$Amount,
                            'user_id'=>auth()->user()->id,
                            'auth'=>$Authority,
                        ]);
                        PayMeta::create([
                            'post_id' => $post->id,
                            'user_id' => auth()->user()->id,
                            'pay_id' => $pay->id,
                            'status'=>100,
                            'price'=> $post->price,
                            'count' => 1,
                            'color' => '[]',
                            'size' => '[]',
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
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>$contents['code'],
                            'property'=>$chars2,
                            'user_id'=>auth()->user()->id,
                            'price'=>$Amount,
                            'auth' => $Authority,
                        ]);
                        PayMeta::create([
                            'post_id' => $post->id,
                            'user_id' => auth()->user()->id,
                            'pay_id' => $pay->id,
                            'status'=>$contents['code'],
                            'price'=> $post->price,
                            'count' => 1,
                            'color' => '[]',
                            'size' => '[]',
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
                    $pay = Pay::create([
                        'refId'=>'',
                        'status'=>0,
                        'price'=>$Amount,
                        'property'=>$chars2,
                        'user_id'=>auth()->user()->id,
                        'auth' => $Authority,
                    ]);
                    PayMeta::create([
                        'post_id' => $post->id,
                        'user_id' => auth()->user()->id,
                        'pay_id' => $pay->id,
                        'status'=>0,
                        'price'=> $post->price,
                        'count' => 1,
                        'color' => '[]',
                        'size' => '[]',
                    ]);
                    auth()->user()->update([
                        'buy'=> null
                    ]);
                    return Inertia::render('Cart/BuyIndex' , [
                        'name' => $name,
                        'pay' => $pay,
                    ]);
                }
            }else {
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function zibal(Request $request){
        $MerchantID = Setting::where('key' , 'zibal')->pluck('value')->first();
        if (auth()->user()->userMeta()->count() >= 1) {
            $post = Post::where('id',auth()->user()->buy)->where('type' , 1)->first();
            if($post){
                $Amount = (int)$post->price;
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
                        $pay = Pay::create([
                            'refId'=>$contents['refNumber'],
                            'status'=> 100,
                            'deliver'=> 4,
                            'property'=>$chars2,
                            'price'=>$Amount,
                            'user_id'=>auth()->user()->id,
                            'auth'=>$Authority,
                        ]);
                        PayMeta::create([
                            'post_id' => $post->id,
                            'user_id' => auth()->user()->id,
                            'pay_id' => $pay->id,
                            'status'=>100,
                            'price'=> $post->price,
                            'count' => 1,
                            'color' => '[]',
                            'size' => '[]',
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
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>$contents['status'],
                            'property'=>$chars2,
                            'user_id'=>auth()->user()->id,
                            'price'=>$Amount,
                            'auth' => $Authority,
                        ]);
                        PayMeta::create([
                            'post_id' => $post->id,
                            'user_id' => auth()->user()->id,
                            'pay_id' => $pay->id,
                            'status'=>$contents['status'],
                            'price'=> $post->price,
                            'count' => 1,
                            'color' => '[]',
                            'size' => '[]',
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
                    $pay = Pay::create([
                        'refId'=>'',
                        'status'=>0,
                        'price'=>$Amount,
                        'property'=>$chars2,
                        'user_id'=>auth()->user()->id,
                        'auth' => $Authority,
                    ]);
                    PayMeta::create([
                        'post_id' => $post->id,
                        'user_id' => auth()->user()->id,
                        'pay_id' => $pay->id,
                        'status'=>0,
                        'price'=> $post->price,
                        'count' => 1,
                        'color' => '[]',
                        'size' => '[]',
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
        } else {
            return redirect('/user-info-cart');
        }
    }

    public function idpay(Request $request){
        $MerchantID = Setting::where('key' , 'idpay')->pluck('value')->first();
        if (auth()->user()->userMeta()->count() >= 1) {
            $post = Post::where('id',auth()->user()->buy)->where('type' , 1)->first();
            if($post){
                $Amount = (int)$post->price;
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
                        $pay = Pay::create([
                            'refId'=>$contents['payment']['track_id'],
                            'status'=> 100,
                            'deliver'=> 4,
                            'property'=>$chars2,
                            'price'=>$Amount,
                            'user_id'=>auth()->user()->id,
                            'auth'=>$Authority,
                        ]);
                        PayMeta::create([
                            'post_id' => $post->id,
                            'user_id' => auth()->user()->id,
                            'pay_id' => $pay->id,
                            'status'=>100,
                            'price'=> $post->price,
                            'count' => 1,
                            'color' => '[]',
                            'size' => '[]',
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
                        $pay = Pay::create([
                            'refId'=>'',
                            'status'=>$contents['status'],
                            'property'=>$chars2,
                            'user_id'=>auth()->user()->id,
                            'price'=>$Amount,
                            'auth' => $Authority,
                        ]);
                        PayMeta::create([
                            'post_id' => $post->id,
                            'user_id' => auth()->user()->id,
                            'pay_id' => $pay->id,
                            'status'=>$contents['status'],
                            'price'=> $post->price,
                            'count' => 1,
                            'color' => '[]',
                            'size' => '[]',
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
                    $pay = Pay::create([
                        'refId'=>'',
                        'status'=>0,
                        'price'=>$Amount,
                        'property'=>$chars2,
                        'user_id'=>auth()->user()->id,
                        'auth' => $Authority,
                    ]);
                    PayMeta::create([
                        'post_id' => $post->id,
                        'user_id' => auth()->user()->id,
                        'pay_id' => $pay->id,
                        'status'=>0,
                        'price'=> $post->price,
                        'count' => 1,
                        'color' => '[]',
                        'size' => '[]',
                    ]);
                    auth()->user()->update([
                        'buy'=> null
                    ]);
                    return Inertia::render('Cart/BuyIndex' , [
                        'name' => $name,
                        'pay' => $pay,
                    ]);
                }
            }else {
                return redirect('/user-info-cart');
            }
        } else {
            return redirect('/user-info-cart');
        }
    }
}
