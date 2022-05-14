<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Charge;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Pay;
use Illuminate\Support\Facades\DB;
use App\Models\PayMeta;

class PayController extends Controller
{
    public function index(Request $request){
        DB::table('pays')->where('seen', 0)->update(['seen' => 1]);
        if($request->value){
            foreach ($request->value as $value) {
                $pay = Pay::where('id', $value)->first();
                $pay->carrier()->detach();
            }
            DB::table('pay_metas')->whereIn('pay_id', $request->value)->delete();
            DB::table('pays')->whereIn('id', $request->value)->delete();
        }
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش پرداختی')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف پرداختی')->pluck('name');
        if(auth()->user()->admin == 1 or count($showSome) >= 1){
            $show = 1;
        }else{
            $show = 0;
        }
        if(auth()->user()->admin == 1 or count($checkDeletes) >= 1){
            $deleted = 1;
        }else{
            $deleted = 0;
        }
        if($request->deliver){
            $payId = Pay::where('id' , $request->deliver['id'])->first();
            $payId->update([
                'deliver' => $request->deliver['deliver']
            ]);
        }
        if($request->sort == 0){
            $sort = Pay::latest()->pluck('id')->toArray();
        }
        if($request->sort == 1){
            $sort = Pay::latest()->where('status' , '!=' , 100)->pluck('id')->toArray();
        }
        if($request->sort == 2){
            $sort = Pay::latest()->where('status' , 100)->pluck('id')->toArray();
        }
        if($request->sortDeliver || $request->sortDeliver === 0){
            if($request->sortDeliver == 5){
                $sortDeliver = Pay::latest()->pluck('id')->toArray();
            }else{
                $sortDeliver = Pay::latest()->where('deliver' , $request->sortDeliver)->pluck('id')->toArray();
            }
        }else{
            $sortDeliver = Pay::latest()->pluck('id')->toArray();
        }
        if($request->date){
            $date = Pay::latest()->whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = Pay::latest()->pluck('id')->toArray();
        }
        if($request->search){
            $search = Pay::latest()->where("property" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            if (count($search) == 0){
                $search = Pay::latest()->where("refId" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            }
        }else{
            $search = Pay::latest()->pluck('id')->toArray();
        }

        $arrayFilter = array_intersect($sort, $sortDeliver,$date,$search);

        if($request->page){
            $pays = Pay::latest()->with('carrier')->with(["payMeta" => function($q){
                $q->with('post');
            }])->whereIn('id' , $arrayFilter)->paginate($request->page);
        }else{
            $pays = Pay::latest()->with('carrier')->with(["payMeta" => function($q){
                $q->with('post');
            }])->whereIn('id' , $arrayFilter)->paginate(25);
        }
        Inertia::setRootView('admin');
        $labels = ['#','شماره ﺗﺮﺍﻛﻨﺶ ﭘﺮﺩﺍﺧﺖ','وضعیت ارسال','مبلغ','شماره سفارش','وضعیت پرداخت','تاریخ ثبت','عملیات'];
        return Inertia::render('PayPanel' , [
            'labels' => $labels,
            'pays' => $pays,
            'show' => $show,
            'deleted' => $deleted,
        ]);
    }

    public function chart(Request $request){
        $tops = Post::withCount('payMeta')->orderBy('pay_meta_count','DESC' )->withCount(["payMeta" => function($q){
            $q->latest()->where('status',100);
        }])->with(["payMeta" => function($q){
            $q->latest()->where('status',100)->whereDate('created_at',Carbon::today());
        }])->take(3)->get();
        $acceptPay = Pay::whereDate('created_at',Carbon::today())->where('status' , 100)->count();
        $unPay = Pay::whereDate('created_at',Carbon::today())->where('status' , '!=' , 100)->count();
        $deliver = Pay::whereDate('created_at',Carbon::today())->where('deliver' , 1)->count();
        $notDeliver = Pay::whereDate('created_at',Carbon::today())->where('deliver' , '!=' , 1)->count();
        $lastPay = PayMeta::whereDate('created_at',Carbon::today())->with('post','user')->where('status' , 100)->take(10)->get();
        $allPrice = Pay::where('status' , 100)->pluck('price')->sum();
        $weekPrice = Pay::where('status' , 100)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->pluck('price')->sum();
        $monthPrice = Pay::where('status' , 100)->whereMonth('created_at', Carbon::now()->month)->pluck('price')->sum();
        $allPay = Pay::where('status' , 100)->count();
        $weekPay = Pay::where('status' , 100)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $monthPay = Pay::where('status' , 100)->whereMonth('created_at', Carbon::now()->month)->count();
        $year = Carbon::now()->format('Y');
        $deyPay = Pay::whereMonth('created_at' , '1')->whereYear('created_at' , $year)->where('status' , '100')->pluck('price')->sum();
        $bahmanPay = Pay::whereMonth('created_at' , '2')->whereYear('created_at' , $year)->where('status' , '100')->pluck('price')->sum();
        $esfandPay = Pay::whereMonth('created_at' , '3')->whereYear('created_at' , $year)->where('status' , '100')->pluck('price')->sum();
        $farvardinPay = Pay::whereMonth('created_at' , '4')->whereYear('created_at' , $year)->where('status' , '100')->pluck('price')->sum();
        $ordibeheshtPay = Pay::whereMonth('created_at' , '5')->whereYear('created_at' , $year)->where('status' , '100')->pluck('price')->sum();
        $khordadPay = Pay::whereMonth('created_at' , '6')->whereYear('created_at' , $year)->where('status' , '100')->pluck('price')->sum();
        $tirPay = Pay::whereMonth('created_at' , '7')->whereYear('created_at' , $year)->where('status' , '100')->pluck('price')->sum();
        $mordadPay = Pay::whereMonth('created_at' , '8')->whereYear('created_at' , $year)->where('status' , '100')->pluck('price')->sum();
        $shahrivarPay = Pay::whereMonth('created_at' , '9')->whereYear('created_at' , $year)->where('status' , '100')->pluck('price')->sum();
        $mehrPay = Pay::whereMonth('created_at' , '10')->whereYear('created_at' , $year)->where('status' , '100')->pluck('price')->sum();
        $abanPay = Pay::whereMonth('created_at' , '11')->whereYear('created_at' , $year)->where('status' , '100')->pluck('price')->sum();
        $azarPay = Pay::whereMonth('created_at' , '12')->whereYear('created_at' , $year)->where('status' , '100')->pluck('price')->sum();
        Inertia::setRootView('admin');
        return Inertia::render('PayChart',[
            'tops' => $tops,
            'acceptPay' => $acceptPay,
            'lastPay' => $lastPay,
            'unPay' => $unPay,
            'deliver' => $deliver,
            'notDeliver' => $notDeliver,
            'allPrice' => $allPrice,
            'weekPrice' => $weekPrice,
            'monthPrice' => $monthPrice,
            'allPay' => $allPay,
            'weekPay' => $weekPay,
            'monthPay' => $monthPay,
            'deyPay' => $deyPay,
            'bahmanPay' => $bahmanPay,
            'esfandPay' => $esfandPay,
            'farvardinPay' => $farvardinPay,
            'ordibeheshtPay' => $ordibeheshtPay,
            'khordadPay' => $khordadPay,
            'tirPay' => $tirPay,
            'mordadPay' => $mordadPay,
            'shahrivarPay' => $shahrivarPay,
            'mehrPay' => $mehrPay,
            'abanPay' => $abanPay,
            'azarPay' => $azarPay,
        ]);
    }

    public function show(Pay $pay){
        $pays = Pay::where('id' , $pay->id)->with('carrier','address')->with(["user" => function($q){
            $q->with('userMeta');
        }])->first();
        $payMeta = PayMeta::where('pay_id' , $pay->id)->with('post')->get();
        return [$pays , $payMeta];
    }

    public function create(Request $request){
        if($request->people){
            $Amount = 0;
            for ( $i = 0; $i < count($request->products); $i++) {
                $Amount = (int)$Amount + ((int)$request->products[$i]['price'] * (int)$request->products[$i]['count']);
            }
            $user = User::where('id' ,$request->people['userId'])->first();
            $address = $user->address()->where('status' , 1)->pluck('id')->first();
            $chars = '012345678901234567890123456789';
            $chars2 = substr(str_shuffle($chars), 0 , 10);
            if (Pay::where('property' , $chars2)->first()){
                $chars2 = substr(str_shuffle($chars), 0 , 10);
                if (Pay::where('property' , $chars2)->first()){
                    $chars2 = substr(str_shuffle($chars), 0 , 10);
                }
            }
            $times= '{"dayL":"","dayLEn":"","price":0,"to":"","from":"","day":"","month":"","monthEn":"","timestamp":""}';
            $pay = Pay::create([
                'refId'=>'',
                'status'=> $request->status,
                'price'=>$Amount,
                'property'=>$chars2,
                'time' => $times,
                'user_id' => $request->people['userId'],
                'auth' => $chars2,
            ]);
            if($address){
                $pay->address()->attach($address);
            }
            if($request->carrier){
                $pay->carrier()->sync($request->carrier);
            }
            for ( $i = 0; $i < count($request->products); $i++) {
                $payMeta = PayMeta::create([
                    'post_id' => $request->products[$i]['productId'],
                    'user_id' => $request->people['userId'],
                    'status'=> $request->status,
                    'pay_id' => $pay->id,
                    'price'=> ((int)$request->products[$i]['price'] * (int)$request->products[$i]['count']),
                    'count' => $request->products[$i]['count'],
                    'color' => json_encode($request->products[$i]['color']),
                    'size' => json_encode($request->products[$i]['size']),
                ]);
                $payMeta->guarantee()->sync($request->products[$i]['guarantee']);
            }
        }
        $allUsers = User::take(50)->get();
        $allProduct = Post::take(50)->with('review','guarantee')->get();
        $carriers = Carrier::take(50)->get();
        Inertia::setRootView('admin');
        return Inertia::render('CreatePay',[
            'allUsers' => $allUsers,
            'allProduct' => $allProduct,
            'carriers' => $carriers
        ]);
    }

    public function showPay(Pay $pay , Request $request){
        if($request->update == 1){
            $changePay = PayMeta::where('pay_id' , $request->pay)->where('id' , $request->payMeta)->first();
            if ($request->change == 0) {
                if ($changePay->count == 1) {
                    $changePay->delete();
                } else {
                    $priceO = $changePay->price / $changePay->count ;
                    $countF = $changePay->count - 1;
                    $allPrice = $priceO * $countF;
                    $changePay->update([
                        'count' => $countF,
                        'price' => $allPrice
                    ]);
                }
            } else {
                $priceO = $changePay->price / $changePay->count ;
                $countF = $changePay->count + 1;
                $allPrice = $priceO * $countF;
                $changePay->update([
                    'count' => $countF,
                    'price' => $allPrice
                ]);
            }
        }
        if($request->update == 5){
            Pay::where('id' , $pay->id)->first()->update([
                'status' => $request->status
            ]);
            DB::table('pay_metas')->where('pay_id' , $pay->id)->update(['status' => $request->status]);
        }
        if($request->update == 7){
            Pay::where('id' , $pay->id)->first()->update([
                'track' => $request->track
            ]);
        }
        if($request->update == 6){
            if($request->back == 1){
                $chars = '012345678901234567890123456789';
                $chars2 = substr(str_shuffle($chars), 0 , 9);
                if (Charge::where('property' , $chars2)->first()){
                    $chars2 = substr(str_shuffle($chars), 0 , 9);
                    if (Charge::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 9);
                    }
                }
                $charge = Charge::create([
                    'price'=> $pay->price,
                    'type'=> 0,
                    'status'=> 100,
                    'property'=> $chars2,
                    'user_id'=> $pay->user_id,
                ]);
            }
            Pay::where('id' , $pay->id)->first()->update([
                'back' => $request->back
            ]);
        }
        if($request->update == 3){
            PayMeta::where('pay_id' , $request->pay)->where('id' , $request->payMeta)->first()->delete();
        }
        if($request->update == 4 && $request->count && $request->product){
            $createPay = PayMeta::create([
                'size' => json_encode($request->size),
                'color' => json_encode($request->color),
                'post_id' => $request->product['id'],
                'count' => $request->count,
                'user_id' => $pay->user_id,
                'pay_id' => $pay->id,
                'price' => $request->product['price'],
                'status' => $pay->status,
            ]);
            $createPay->guarantee()->sync($request->guarantee);
        }
        if($request->update == 2){
            $pay->update([
               'deliver' => $request->deliver
            ]);
            DB::table('pay_metas')->where('pay_id' , $pay->id)->update(['deliver' => $request->deliver]);
        }
        $pays = Pay::where('id' , $pay->id)->with('carrier','address')->with(["user" => function($q){
            $q->with('userMeta');
        }])->with(["payMeta" => function($q){
            $q->with('post');
        }])->first();
        $map = Setting::where('key' , 'map')->pluck('value')->first();
        $products = Post::where('variety' , '0')->where('type' , 0)->with('review','guarantee')->take(100)->get();
        Inertia::setRootView('admin');
        return Inertia::render('ShowPayPanel',[
            'pay' => $pays,
            'map' => $map,
            'products' => $products,
        ]);
    }

    public function invoice(Pay $pay){
        $carrier = Carrier::with(["pay" => function ($q) use ($pay){
            $q->where('id',$pay->id);
        }])->get();
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first();
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $email = Setting::where('key' , 'email')->pluck('value')->first();
        $number = Setting::where('key' , 'number')->pluck('value')->first();
        $pays = Pay::with('carrier','address')->where('id',$pay->id)->with(["payMeta" => function($q){
            $q->with('guarantee')->with(["post" => function($q){
                $q->with('user');
            }]);
        }])->with(["user" => function($q){
            $q->with('userMeta');
        }])->first();
        return Inertia::render('Home/User/InvoicePay', [
            'pay' => $pays,
            'title' => $title,
            'number' => $number,
            'email' => $email,
            'address' => $address,
            'logo' => $logo,
            'carrier' => $carrier,
        ]);
    }
}
