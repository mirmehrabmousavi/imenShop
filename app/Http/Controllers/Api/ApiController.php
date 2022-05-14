<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Models\Score;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Category;
use App\Models\ActiveSms;
use App\Models\User;
use App\Models\Time;
use App\Models\Carrier;
use App\Models\Event;
use App\lib\zarinpal;
use App\lib\zibal;
use App\lib\nextpay;
use App\lib\idpay;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Cache;
use App\Traits\SendEmailTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Brand;
use App\Models\Rate;
use App\Models\Pay;
use App\Models\PayMeta;
use App\Models\UserMeta;
use App\Models\Like;
use Illuminate\Support\Facades\DB;
use App\Models\Vidget;
use Carbon\Carbon;
use App\Models\Cart;
use Illuminate\Support\Facades\Hash;
use App\Models\Bookmark;
use Melipayamak\MelipayamakApi;
use nusoap_client;

class ApiController extends Controller
{

    use SendEmailTrait;
    public function allProduct(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $time = Carbon::now()->format('Y-m-d h:i');
            DB::table('posts')->where('suggest', '<=' , $time)->update(['suggest'=> null]);

            $vidgets = Vidget::where('platform' , 1)->get();
            $vidget = [];
            foreach ($vidgets as $item){
                $vidgetCategory = [
                    'id'=> $item['id'],
                    'name'=> $item['name'],
                    'title'=> $item['title'],
                    'more'=> $item['more'],
                    'titleEn'=> $item['titleEn'],
                    'moreEn'=> $item['moreEn'],
                    'slug'=> $item['slug'],
                    'background'=> $item['background'],
                    'show'=> $item['show'],
                    'type'=> $item['type'],
                    'count'=> $item['count'],
                    'post'=> [],
                    'pay'=> [],
                ];
                $ids = [];
                $ids2 = [];
                if($item['category'] != '' && $item['name'] != 'تبلیغات ساده' && $item['name'] != 'اسلایدر بزرگ تبلیغ' && $item['name'] != 'تبلیغات اسلایدری'){
                    $allCatSite3 = explode(',' , $item['category']);
                    foreach ($allCatSite3 as $value){
                        $tax = Category::where('name' , $value)->first();
                        $send2 = $tax->post()->where('type' , 0)->where('variety' , 0)->pluck('id')->toArray();
                        foreach ($send2 as $data){
                            array_push($ids ,$data);
                        }
                    }
                }
                if($item['brand'] != '' && $item['name'] != 'تبلیغات ساده' && $item['name'] != 'پیشنهاد شگفت انگیز' && $item['name'] != 'اسلایدر بزرگ تبلیغ' && $item['name'] != 'تبلیغات اسلایدری'){
                    $allBrandSite3 = explode(',' , $item['brand']);
                    foreach ($allBrandSite3 as $value){
                        $tax = Brand::where('name' , $value)->first();
                        $send2 = $tax->post()->where('type' , 0)->where('variety' , 0)->pluck('id')->toArray();
                        foreach ($send2 as $data){
                            array_push($ids2 ,$data);
                        }
                    }
                }
                if($item['category'] == ''){
                    $ids = Post::latest()->where('type' , 0)->where('variety' , 0)->pluck('id')->toArray();
                }
                if($item['brand'] == '' || $item['name'] == 'پیشنهاد شگفت انگیز'){
                    $ids2 = Post::latest()->where('type' , 0)->where('variety' , 0)->pluck('id')->toArray();
                }
                $arrayFilter = array_intersect($ids2, $ids);
                if ($item['show'] == 0){
                    if($item['type'] == 3){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->latest()->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 0){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->latest()->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 1){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->latest()->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 2){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->latest()->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                }
                if ($item['show'] == 1 or $item['show'] == 2){
                    if($item['type'] == 3){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->withCount('view')->orderBy('view_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 0){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->withCount('view')->orderBy('view_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 1){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->withCount('view')->orderBy('view_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 2){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->withCount('view')->orderBy('view_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                }
                if ($item['show'] == 3){
                    if($item['type'] == 3){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->orderBy('price')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 0){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->orderBy('price')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 1){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->orderBy('price')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 2){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->orderBy('price')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                }
                if ($item['show'] == 4){
                    if($item['type'] == 3){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->orderBy('price','DESC')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 0){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->orderBy('price','DESC')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 1){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->orderBy('price','DESC')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 2){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->orderBy('price','DESC')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                }
                if ($item['show'] == 5){
                    if($item['type'] == 3){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 0){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 1){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                    if($item['type'] == 2){
                        $catPost = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                            $q->where('status' ,100);
                        }])->get();
                    }
                }
                $vidgetCategory['post'] = $catPost;
                if($item['name'] == 'پست افقی'){
                    foreach($catPost as $items){
                        $counts = 0;
                        if($items['payMeta']){
                            foreach($items['payMeta'] as $value){
                                $counts = $counts + $value['count'];
                            }
                        }
                        array_push($vidgetCategory['pay'] , $counts);
                    }
                }
                if($item['name'] == 'برند ویژه'){
                    $vidgetCategory['post'] = [];
                    $allCatSite3 = explode(',' , $item['brand']);
                    $brands = Brand::whereIn('name',$allCatSite3)->withCount(["post" => function($q){
                        $q->where('status' , 1);
                    }])->latest()->get();
                    $vidgetCategory['post'] = $brands;
                }

                if($item['name'] == 'تبلیغات ساده' || $item['name'] == 'اسلایدر بزرگ تبلیغ'){
                    $vidgetCategory['post'] = [];
                    $vidgetCategory['post'] = $item['brand'];
                }
                if($item['name'] == 'پیشنهاد شگفت انگیز'){
                    $vidgetCategory['titleEn'] = [];
                    $vidgetCategory['titleEn'] = $item['brand'];
                }
                if($item['name'] == 'تبلیغات اسلایدری'){
                    $vidgetCategory['post'] = [];
                    $vidgetCategory['title'] = [];
                    $vidgetCategory['post'] = $item['brand'];
                    $vidgetCategory['title'] = $item['category'];
                }
                array_push($vidget , $vidgetCategory);
            }
            return response()->json(['vidget' => $vidget] , 200);
        }
    }

    public function single(Post $post,Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $time = Carbon::now()->format('Y-m-d h:i');
            DB::table('posts')->where('suggest', '<=' , $time)->update(['suggest'=> null]);
            $users = User::get();
            foreach ($users as $user) {
                if (Cache::has('user-is-online-' . $user->id)){
                    $user->update([
                        'activity' => Verta::now()->format('H:i Y-n-j')
                    ]);
                }
            }

            $approve = Setting::where('key' , 'approved')->pluck('value')->first();
            if ($approve == 0){
                $posts = Post::where('id' , $post->id)->with('tag' , 'guarantee' , 'review' , 'category' , 'brand','comments')->first();
            }else{
                $posts = Post::where('id' , $post->id)->with('tag' , 'guarantee' , 'review' , 'category' , 'brand')->with(["comments" => function($q){
                    $q->where('approved', '=', 1);
                }])->first();
            }

            $showPostCategory = Setting::where('key' , 'showPostCategory')->pluck('value')->first();

            $related =  Post::whereHas('category', function ($q) use ($post){
                return $q->whereIn('name', $post->category()->pluck('name'));
            })->where('id' , '!=' , $post->id)->where('status' , 1)->take($showPostCategory)->get();
            $rates=[];
            $array = Rate::where('post_id' , $post->id)->pluck('rate_post');
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

            if ($approve == 0){
                $comment = $post->comments()->latest()->with(["comments" => function($q){
                    $q->latest();
                }])->get();
            }else{
                $comment = $post->comments()->latest()->where('approved' , 1)->with(["comments" => function($q){
                    $q->where('approved', '=', 1)->latest();
                }])->where('parent_id' , 1)->get();
            }

            $reply = Setting::where('key' , 'reply')->pluck('value')->first();
            $coercion = Setting::where('key' , 'coercion')->pluck('value')->first();
            $showUser = Setting::where('key' , 'showUser')->pluck('value')->first();
            $checkOnline = Setting::where('key' , 'checkOnline')->pluck('value')->first();

            return response()->json(['reply' => $reply,'coercion' => $coercion,'showUser' => $showUser,'checkOnline' => $checkOnline,'posts' => $posts,'related' => $related,'rate' => $average,'comment' => $comment] , 200);
        }
    }

    public function login(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];

            if (auth()->attempt($credentials)) {
                $token = auth()->user()->createToken('TutsForWeb')->accessToken;
                $user = auth()->user();
                return response()->json(['token' => $token,'user' => $user], 200);
            } else {
                return response()->json(['error' => 'UnAuthorised'], 401);
            }
        }
    }

    public function register(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $users = User::where('name' , $request->name)->first();
            if($users){
                return 'name';
            }
            else{
                User::create([
                    'name'=> $request->name,
                    'email'=> $request->email,
                    'password'=> Hash::make($request->password),
                ]);

                $credentials = [
                    'email' => $request->email,
                    'password' => $request->password
                ];

                if (auth()->attempt($credentials)) {
                    $token = auth()->user()->createToken('TutsForWeb')->accessToken;
                    $user = auth()->user();
                    return response()->json(['token' => $token,'user' => $user], 200);
                } else {
                    return response()->json(['error' => 'UnAuthorised'], 401);
                }
            }
        }
    }

    public function option(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->user)->pluck('id')->first();
            $like = Like::where('user_id' , $user)->where('post_id' , $request->post)->first();
            if($like){
                $likes = 1;
            }else{
                $likes = 0;
            }
            $bookmark = Bookmark::where('user_id' , $user)->where('post_id' , $request->post)->first();
            if($bookmark){
                $bookmarks = 1;
            }else{
                $bookmarks = 0;
            }
            return response()->json(['like' => $likes,'bookmark' => $bookmarks] , 200);
        }
    }

    public function sendLike(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $credentials = [
                'user' => $request->user,
                'post' => $request->post
            ];
            $user = User::where('email' , $credentials['user'])->pluck('id')->first();
            $like = Like::where('user_id' , $user)->where('post_id' , $credentials['post'])->first();
            if($like){
                $like->delete();
                $likes =  0;
            }else{
                Like::create([
                    'user_id'=>$user,
                    'post_id'=>$request->post,
                ]);
                $likes =  1;
            }
            return response()->json(['like' => $likes] , 200);
        }
    }

    public function sendBookmark(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $credentials = [
                'user' => $request->user,
                'post' => $request->post
            ];
            $user = User::where('email' , $credentials['user'])->pluck('id')->first();
            $bookmark = Bookmark::where('user_id' , $user)->where('post_id' , $credentials['post'])->first();
            if($bookmark){
                $bookmark->delete();
                $bookmarks =  0;
            }else{
                Bookmark::create([
                    'user_id'=>$user,
                    'post_id'=>$request->post,
                ]);
                $bookmarks =  1;
            }
            return response()->json(['bookmark' => $bookmarks] , 200);
        }
    }

    public function getUser(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->user)->with('userMeta')->withCount('comments','pay' , 'like','bookmark')->first();
            return response()->json(['user' => $user] , 200);
        }
    }

    public function getUserLike(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->user)->first();
            $likes = $user->like;
            $likePost = [];
            foreach ($likes as $item) {
                $posts = Post::latest()->where('id' , $item->post_id)->where('type' , 0)->pluck('id')->first();
                array_push($likePost , $posts);
            }
            $likePosts = Post::latest()->whereIn('id' , $likePost)->where('type' , 0)->get();

            return response()->json(['likePosts' => $likePosts] , 200);
        }
    }

    public function getUserBookmark(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->user)->first();
            $bookmarks = $user->bookmark;
            $bookmarkPost = [];
            foreach ($bookmarks as $item) {
                $posts = Post::latest()->where('id' , $item->post_id)->where('type' , 0)->pluck('id')->first();
                array_push($bookmarkPost , $posts);
            }
            $bookmarkPosts = Post::latest()->whereIn('id' , $bookmarkPost)->where('type' , 0)->get();

            return response()->json(['bookmarkPosts' => $bookmarkPosts] , 200);
        }
    }

    public function getUserPay(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->user)->first();
            $pay = Pay::latest()->where('user_id' , $user->id)->get();

            return response()->json(['pay' => $pay] , 200);
        }
    }

    public function changeUserInfo(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->user)->first();
            if ($user->userMeta){
                if($request->password){
                    $user->update([
                        'password'=>Hash::make($request->password),
                    ]);
                }
                $user->userMeta()->update([
                    'name'=>$request->name,
                    'post'=>$request->post,
                    'job'=>$request->job,
                    'code'=>$request->code,
                    'address'=>$request->address,
                ]);
                return redirect('/profile/personal-info');
            }
            else{
                if($request->password){
                    $user->update([
                        'password'=>Hash::make($request->password),
                    ]);
                }
                $userMeta = UserMeta::create([
                    'name'=>$request->name,
                    'post'=>$request->post,
                    'job'=>$request->job,
                    'code'=>$request->code,
                    'address'=>$request->address,
                ]);
                $user->userMeta()->sync($userMeta->id);
                return redirect('/profile/personal-info');
            }
            return response()->json(['user' => $user] , 200);
        }
    }

    public function getPayMeta(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $payMeta = PayMeta::latest()->where('pay_id' , $request->pay)->with('post')->get();
            $pay = Pay::latest()->where('id' , $request->pay)->with(["user" => function($q){
                $q->with('userMeta')->get();
            }])->first();
            return response()->json(['payMeta' => $payMeta , 'pay' => $pay] , 200);
        }
    }

    public function addCart(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->user)->first();
            $cart = Cart::where('post_id' , $request->postID)->where('guarantee_id', $request->guarantee)->where('user_id' , $user->id)->where('size', json_encode($request->sizeName))->where('color', json_encode($request->colorName))->get();
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
                $check = Cart::where('post_id' , $request->postID)->where('user_id' , $user->id)->where('guarantee_id', $request->guarantee)->where('color', json_encode($request->colorName))->where('size', json_encode($request->sizeName))->first();
                if ($check) {
                    $check->update([
                        'count' => ++$check->count
                    ]);
                    return response()->json(['message' => 'کالا افزوده شد'], 200);
                } else {
                    if($request->colorName){
                        $sendColor= json_encode($request->colorName);
                    }else{
                        $sendColor= '[]';
                    }
                    if($request->sizeName){
                        $sendSize= json_encode($request->sizeName);
                    }else{
                        $sendSize= '[]';
                    }
                    Cart::create([
                        'post_id' => $request->postID,
                        'user_id' => $user->id ,
                        'guarantee_id' => $request->guarantee,
                        'color' => $sendColor,
                        'size' => $sendSize,
                        'price' => $request->price,
                        'count' => '1',
                    ]);
                    $post = Post::where('id', $request->postID)->where('type' , 0)->pluck('title')->first();
                    Event::create([
                        'type' => 3,
                        'title' => 'سبد خرید',
                        'user_id' => $user->id ,
                        'description' => 'کاربر با نام ' . $user->name . ' محصول ' . $post . ' را به سبد خرید اضافه کرد',
                    ]);
                }
            } else {
                return response()->json(['message' => 'تعداد کافی نمیباشد'] , 401);
            }
        }
    }

    public function allCategory(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $showPostCategory = Setting::where('key' , 'showPostCategory')->pluck('value')->first();

            $allBrands = Brand::where('image' , '!=' , null)->latest()->take($showPostCategory)->get();

            $catHeader1 = Setting::where('key' , 'catHeader')->pluck('value')->first();
            $catPost = [];
            if ($catHeader1 != null){
                $allCatHeader1 = explode('[' , $catHeader1);
                $allCatHeader2 = explode(']' , $allCatHeader1[1]);
                $allCatHeader3 = explode(',' , $allCatHeader2[0]);
                foreach ($allCatHeader3 as $item){
                    $send = Category::where('id' , $item)->with(["cats" => function($q){
                        $showPostCategory = Setting::where('key' , 'showPostCategory')->pluck('value')->first();
                        $q->with(["post" => function($q){
                            $q->where('type' ,0);
                        }])->latest()->take($showPostCategory);
                    }])->first();
                    array_push($catPost ,$send);
                }
            }
            return response()->json(['catPost' => $catPost,'allBrands' => $allBrands] , 200);
        }
    }

    public function category(Category $category , Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $cats = Category::where('id' , $category->id)->with('cats')->first();

            $off = $category->post()->where('type' , 0)->where('variety' , 0)->where('status', 1)->where('off' , '!=' , null)->take(16)->get();

            $sell = $category->post()->where('type' , 0)->where('variety' , 0)->where('status', 1)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->take(16)->get();

            $view = $category->post()->where('type' , 0)->where('variety' , 0)->where('status', 1)->withCount('view')->orderBy('view_count','DESC' )->take(16)->get();

            $categories = Category::where('id' , $category->id)->with(["post" => function($q){
                $q->where('status' , 1)->where('type' , 0)->where('variety' , 0)->latest();
            }])->first();
            return response()->json(['categories' => $categories , 'sell' => $sell , 'view' => $view , 'off' => $off , 'cats' => $cats] , 200);
        }
    }

    public function vidget(Vidget $vidget , Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $ids = [];
            $ids2 = [];
            if($vidget['category'] != null){
                $allCatSite3 = explode(',' , $vidget['category']);
                foreach ($allCatSite3 as $value){
                    $tax = Category::where('name' , $value)->first();
                    $send2 = $tax->post()->where('type' , 0)->where('variety' , 0)->pluck('id')->toArray();
                    foreach ($send2 as $data){
                        array_push($ids ,$data);
                    }
                }
            }
            if($vidget['brand'] != ''){
                $allBrandSite3 = explode(',' , $vidget['brand']);
                foreach ($allBrandSite3 as $value){
                    $tax = Brand::where('name' , $value)->first();
                    $send2 = $tax->post()->where('type' , 0)->where('variety' , 0)->pluck('id')->toArray();
                    foreach ($send2 as $data){
                        array_push($ids2 ,$data);
                    }
                }
            }
            if($vidget['category'] == ''){
                $ids = Post::pluck('id')->where('type' , 0)->where('variety' , 0)->toArray();
            }
            if($vidget['brand'] == ''){
                $ids2 = Post::pluck('id')->where('type' , 0)->where('variety' , 0)->toArray();
            }
            $id3 = array_intersect($ids,$ids2);

            $cats = [];

            $off = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('status', 1)->where('off' , '!=' , null)->take(16)->get();

            $sell = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('status', 1)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->take(16)->get();

            $view = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->withCount('view')->where('status', 1)->orderBy('view_count','DESC' )->take(16)->get();

            $categories = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('status', 1)->get();
            return response()->json(['categories' => $categories , 'sell' => $sell , 'view' => $view , 'off' => $off , 'cats' => $cats] , 200);
        }
    }

    public function brand(Brand $brand , Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $cats = Brand::where('id' , $brand->id)->first();

            $off = $brand->post()->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->where('status', 1)->take(16)->get();

            $sell = $brand->post()->where('type' , 0)->where('variety' , 0)->withCount('payMeta')->where('status', 1)->orderBy('pay_meta_count','DESC' )->take(16)->get();

            $view = $brand->post()->where('type' , 0)->where('variety' , 0)->withCount('view')->where('status', 1)->orderBy('view_count','DESC' )->take(16)->get();

            $categories = Brand::where('id' , $brand->id)->with(["post" => function($q){
                $q->where('status' , 1)->where('type' , 0)->where('variety' , 0)->latest();
            }])->first();
            return response()->json(['categories' => $categories , 'sell' => $sell , 'view' => $view , 'off' => $off , 'cats' => $cats] , 200);
        }
    }
    public function changeCarrier(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->email)->first();
            foreach ($user->cart as $value) {
                $value->carrier()->detach();
                $value->carrier()->sync($request->carrier);
            }
        }
    }

    public function changeTimeDelivery(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->email)->first();
            foreach ($user->cart as $value) {
                $value->update([
                    'delivery' => $request->time
                ]);
            }
        }
    }
    public function getCart(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->user)->first();

            $count = Cart::where('user_id' , $user->id)->with('guarantee','carrier')->get();
            for ($i = 0; $i < count($count); $i++) {
                $countCheck = Post::where('id', $count[$i]->post_id)->pluck('count')->first();
                $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                $postSize = [];
                $postColor = [];
                $price = $post['price'];
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
            $cart = $user->cart()->pluck('post_id');
            $carts = [];
            foreach ($cart as $item) {
                $send = Post::where('id', $item)->first();
                array_push($carts, $send);
            };
            $allSum = 0;
            for ( $i = 0; $i < count($count); $i++) {
                $allSum2 = (int)$count[$i]['price'] * (int)$count[$i]['count'];
                $allSum = $allSum + (int)$allSum2;
            };
            $allPay = (int)$allSum;
            $posts = Post::whereIn('id', $cart)->with('time')->get();
            $ids = [];
            foreach ($posts as $item){
                if(count($item['time']) >= 1){
                    $id = $item['time'][0]['id'];
                    array_push($ids , $id);
                }
            }
            $times = Time::whereIn('id' , $ids)->orderBy('day','DESC' )->first();
            $days = [];
            if ($times){
                for ( $i = $times['day']; $i < $times['day']+5; $i++) {
                    $v = new Verta('+'.++$i . "day");
                    $day = Carbon::instance($v)->format('l');
                    --$i;
                    $date = [
                        'dayL'=> '',
                        'dayLEn'=> $day,
                        'price'=> 0,
                        'to'=> $times['to'],
                        'from'=> $times['from'],
                        'day'=> $v->day-1,
                        'month'=> $v->format('%B'),
                        'monthEn'=> Carbon::now()->addDays($i)->format('M'),
                        'timestamp'=> Carbon::now()->addDays($i)->timestamp,
                    ];
                    if($day == 'Saturday'){
                        $date['dayL'] = 'شنبه';
                    }
                    if($day == 'Sunday'){
                        $date['dayL'] = 'یکشنبه';
                    }
                    if($day == 'Monday'){
                        $date['dayL'] = 'دوشنبه';
                    }
                    if($day == 'Tuesday'){
                        $date['dayL'] = 'سه شنبه';
                    }
                    if($day == 'Wednesday'){
                        $date['dayL'] = 'چهار شنبه';
                    }
                    if($day == 'Thursday'){
                        $date['dayL'] = 'پنجشنبه';
                    }
                    if($day == 'Friday'){
                        $date['dayL'] = 'جمعه';
                    }
                    array_push($days , $date);
                }
            }
            $carriers = Carrier::latest()->get();
            $check = $user->cart;
            if(count($check) >= 1){
                if($check[0]->carrier != ''){
                    foreach ($check as $item){
                        $item->carrier()->sync($carriers[0]['id']);
                    }
                }
            }
            return response()->json(['carts' => $carts,'count' => $count, 'allSum' => $allSum,'allPay' => $allPay, 'carriers' => $carriers, 'days' => $days], 200);
        }
    }

    public function increaseCart(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $cart = Cart::where('id' , $request->cart)->first();
            $post = Post::where('id' , $cart->post_id)->pluck('count')->first();
            if ($post - $cart->count >= 1) {
                $cart->update([
                    'count'=> ++$cart->count
                ]);
            }
            return response()->json(['cart' => $cart], 200);
        }
    }

    public function decreaseCart(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $cart = Cart::where('id' , $request->cart)->first();
            if ($cart->count == 1){
                $cart->delete();
            }else{
                $cart->update([
                    'count'=> --$cart->count
                ]);
            }

            return response()->json(['cart' => $cart], 200);
        }
    }

    public function deleteCart(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $cart = Cart::where('id' , $request->cart)->first();
            $cart->delete();
            return response()->json(['cart' => 'deleted'], 200);
        }
    }

    public function search(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $products = Post::where('status' , 1)->where('type' , 0)->where('variety' , 0)->where("title" , "LIKE" , "%{$request->search}%")->get();
            return response()->json(['products' => $products], 200);
        }
    }

    public function getSiteInfo(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $title = Setting::where('key' , 'name')->pluck('value')->first();
            $telegram = Setting::where('key' , 'telegram')->pluck('value')->first();
            $instagram = Setting::where('key' , 'instagram')->pluck('value')->first();
            $twitter = Setting::where('key' , 'twitter')->pluck('value')->first();
            $facebook = Setting::where('key' , 'facebook')->pluck('value')->first();
            $choicePay = Setting::where('key' , 'choicePayApp')->pluck('value')->first();
            return response()->json(['title' => $title,'telegram' => $telegram,'instagram' => $instagram,'twitter' => $twitter,'facebook' => $facebook,'choicePay' => $choicePay], 200);
        }
    }

    public function sendCode(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            DB::table('active_sms')->where('expire' , '<=', Carbon::now()->timestamp)->delete();
            $user = User::where('email' , $request->email)->first();
            if($user){
                return response()->json(['message' => "exist"], 200);
            }else{
                $code = ActiveSms::buildCode();
                ActiveSms::create([
                    'code'=> $code,
                    'expire'=> Carbon::now()->addSecond(120)->timestamp,
                    'phone'=>$request->email
                ]);
                $message = "کد تایید شما برای ورود به وبسایت : $code";
                $this->sendEmail($request->email , $message , 'کد تایید');
                return 2;
            }
        }
    }

    public function checkCode(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $check = ActiveSms::where('code',$request->code)->where('expire' , '>='  ,Carbon::now()->timestamp)->where('phone',$request->email)->first();
            if ($check){
                return 3;
            }else{
                return 2;
            }
        }
    }

    public function order(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->user)->first();
            $cart = $user->cart;
            if($cart[0]->delivery && $cart[0]->carrier) {
                if ($user->userMeta()->count() >= 1 && $user->cart()->count() >= 1) {
                    $number = $user->pluck('number')->first();
                    $count = Cart::where('user_id' , $user->id)->with('guarantee','carrier')->get();
                    for ($i = 0; $i < count($count); $i++) {
                        $countCheck = Post::where('id', $count[$i]->post_id)->pluck('count')->first();
                        $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                        $postSize = [];
                        $postColor = [];
                        $myScore = Score::latest()->where('type' , 0)->where('user_id' , $user->id)->pluck('name')->sum();
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
                    };
                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $Amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $Amount = (int)$Amount + (int)$sends;
                    $order = new zarinpal();
                    $res = $order->pay($Amount, $user->email, $number , '/order');
                    return response()->json(['res' => $res], 200);
                } else {
                    return redirect('/user-info-cart');
                }
            }
        }
    }

    public function nextpay(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->user)->first();
            $cart = $user->cart;
            if($cart[0]->delivery && $cart[0]->carrier) {
                if ($cart = $user->userMeta()->count() >= 1 && $user->cart()->count() >= 1) {
                    $number = $user->pluck('number')->first();
                    $count = Cart::where('user_id' , $user->id)->with('guarantee','carrier')->get();
                    for ($i = 0; $i < count($count); $i++) {
                        $countCheck = Post::where('id', $count[$i]->post_id)->pluck('count')->first();
                        $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                        $postSize = [];
                        $postColor = [];
                        $myScore = Score::latest()->where('type' , 0)->where('user_id' , $user->id)->pluck('name')->sum();
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
                    };
                    $sends1 = $count[0]['carrier'][0];
                    if($sends1['limit'] <= $Amount){
                        $sends = 0;
                    }else{
                        $sends = $count[0]['carrier'][0]['price'];
                    }
                    $Amount = (int)$Amount + (int)$sends;
                    $order = new nextpay();
                    $res = $order->pay($Amount, $user->email, $number , '/order/nextPay');
                    return response()->json(['res' => $res], 200);
                } else {
                    return redirect('/user-info-cart');
                }
            }
        }
    }

    public function successPay(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $MerchantID = Setting::where('key' , 'merchantID')->pluck('value')->first();
            $user = User::where('email' , $request->user)->first();
            $cart = $user->cart;
            if($cart[0]->delivery && $cart[0]->carrier) {
                $count = Cart::where('user_id' , $user->id)->with('guarantee','carrier')->get();
                for ($i = 0; $i < count($count); $i++) {
                    $countCheck = Post::where('id', $count[$i]->post_id)->pluck('count')->first();
                    $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                    $postSize = [];
                    $postColor = [];
                    $myScore = Score::latest()->where('type' , 0)->where('user_id' , $user->id)->pluck('name')->sum();
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
                };
                $sends1 = $count[0]['carrier'][0];
                if($sends1['limit'] <= $Amount){
                    $sends = 0;
                }else{
                    $sends = $count[0]['carrier'][0]['price'];
                }
                $Amount = (int)$Amount + (int)$sends;
                $carts = Cart::where('user_id', $user->id)->with('guarantee')->get();

                $client = new nusoap_client('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
                $client->soap_defencoding = 'UTF-8';

                $result = $client->call('PaymentVerification', [
                    [
                        'MerchantID' => $MerchantID,
                        'Authority' => $request->authority,
                        'Amount' => $Amount,
                    ],
                ]);

                if ($result['Status'] == 100) {
                    $chars = '012345678901234567890123456789';
                    $chars2 = substr(str_shuffle($chars), 0, 10);
                    if (Pay::where('property', $chars2)->first()) {
                        $chars2 = substr(str_shuffle($chars), 0, 10);
                        if (Pay::where('property', $chars2)->first()) {
                            $chars2 = substr(str_shuffle($chars), 0, 10);
                        }
                    }
                    $pay = Pay::create([
                        'refId' => $result['RefID'],
                        'time' => $count[0]->delivery,
                        'status' => $result['Status'],
                        'property' => $chars2,
                        'price' => $Amount,
                        'user_id' => $user->id,
                        'auth' => $request->authority,
                    ]);
                    for ($i = 0; $i < count($carts); $i++) {
                        $count2 = Post::where('id', $carts[$i]->post_id)->pluck('count')->first();
                        Post::where('id', $carts[$i]->post_id)->first()->update([
                            'count' => $count2 - $carts[$i]->count
                        ]);
                        $payMeta = PayMeta::create([
                            'post_id' => $carts[$i]->post_id,
                            'user_id' => $carts[$i]->user_id,
                            'pay_id' => $pay->id,
                            'price'=>$carts[$i]->price,
                            'status' => $result['Status'],
                            'count' => $carts[$i]->count,
                            'color' => $carts[$i]->color,
                            'size' => $carts[$i]->size,
                        ]);
                        $payMeta->guarantee()->sync($carts[$i]->guarantee_id);

                        $post = Post::where('id', $carts[$i]->post_id)->with('review')->first();
                        if ($carts[$i]['color'] != '[]') {
                            $cartColor = json_decode($carts[$i]['color'], true)['name'];
                            $colors = [];
                            foreach (json_decode($post['review'][0]['colors'], true) as $item) {
                                if ($item['name'] == $cartColor) {
                                    $item['count'] = (int)$item['count'] - (int)$carts[$i]['count'];
                                }
                                array_push($colors, $item);
                            }
                            $post->review()->first()->update([
                                'colors' => json_encode($colors),
                            ]);
                        }
                        if ($carts[$i]['size'] != '[]') {
                            $cartSize = json_decode($carts[$i]['size'], true)['name'];
                            $sizes = [];
                            foreach (json_decode($post['review'][0]['size'], true) as $item) {
                                if ($item['name'] == $cartSize) {
                                    $item['count'] = (int)$item['count'] - (int)$carts[$i]['count'];
                                }
                                array_push($sizes, $item);
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
                    $user->cart()->delete();
                    $address = Setting::where('key' , 'address')->pluck('value')->first();
                    $link = $address."show-pay/$pay->property";
                    if($user->email){
                        $text2 = "<strong>سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به آدرس زیر برین : </strong><br/> <a href='$link'>پیگیری پرداختی</a>";
                        $this->sendEmail($user->email , $text2 , 'خرید موفقیت آمیز');
                    }
                    if($user->number){
                        $text2 = "سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به پنل کاربریتان مراجعه کنید";
                        $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                        if($sms == 1){
                            $username = '';
                            $password = '';
                            $api = new MelipayamakApi($username,$password);
                            $sms = $api->sms();
                            $to = $user->number;
                            $from = '';
                            $text = $text2;
                            $response = $sms->send($to,$from,$text);
                        }else{
                            $this->sendSms($user->number , $text2,env('GHASEDAKAPI_Number'));
                        }
                    }
                    if($score >= 1){
                        Score::create([
                            'name'=>$score,
                            'user_id'=>$user->id,
                        ]);
                    }
                    return response()->json(['pay' => $pay], 200);
                } else {
                    $chars = '012345678901234567890123456789';
                    $chars2 = substr(str_shuffle($chars), 0, 10);
                    if (Pay::where('property', $chars2)->first()) {
                        $chars2 = substr(str_shuffle($chars), 0, 10);
                        if (Pay::where('property', $chars2)->first()) {
                            $chars2 = substr(str_shuffle($chars), 0, 10);
                        }
                    }
                    $pay = Pay::create([
                        'refId' => '',
                        'status' => $result['Status'],
                        'property' => $chars2,
                        'user_id' => $user->id,
                        'price' => $Amount,
                        'auth' => $request->authority,
                    ]);
                    for ($i = 0; $i < count($carts); $i++) {
                        $payMeta = PayMeta::create([
                            'post_id' => $carts[$i]->post_id,
                            'user_id' => $carts[$i]->user_id,
                            'status' => $result['Status'],
                            'pay_id' => $pay->id,
                            'price'=>$carts[$i]->price,
                            'count' => $carts[$i]->count,
                            'color' => $carts[$i]->color,
                            'size' => $carts[$i]->size,
                        ]);
                        $payMeta->guarantee()->sync($carts[$i]->guarantee_id);
                    }
                    return response()->json(['pay' => $pay], 100);
                }
            }
        }
    }

    public function failPay(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token) {
            $user = User::where('email', $request->user)->first();
            $cart = $user->cart;
            if ($cart[0]->delivery && $cart[0]->carrier) {
                $count = Cart::where('user_id' , $user->id)->with('guarantee','carrier')->get();
                for ($i = 0; $i < count($count); $i++) {
                    $countCheck = Post::where('id', $count[$i]->post_id)->pluck('count')->first();
                    $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                    $postSize = [];
                    $postColor = [];
                    $myScore = Score::latest()->where('type' , 0)->where('user_id' , $user->id)->pluck('name')->sum();
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
                };
                $sends1 = $count[0]['carrier'][0];
                if($sends1['limit'] <= $Amount){
                    $sends = 0;
                }else{
                    $sends = $count[0]['carrier'][0]['price'];
                }
                $Amount = (int)$Amount + (int)$sends;
                $carts = Cart::where('user_id', $user->id)->with('guarantee')->get();
                $chars = '012345678901234567890123456789';
                $chars2 = substr(str_shuffle($chars), 0, 10);
                if (Pay::where('property', $chars2)->first()) {
                    $chars2 = substr(str_shuffle($chars), 0, 10);
                    if (Pay::where('property', $chars2)->first()) {
                        $chars2 = substr(str_shuffle($chars), 0, 10);
                    }
                }
                $pay = Pay::create([
                    'refId' => '',
                    'status' => -33,
                    'property' => $chars2,
                    'user_id' => $user->id,
                    'price' => $Amount,
                    'auth' => $request->authority,
                ]);
                for ($i = 0; $i < count($carts); $i++) {
                    $payMeta = PayMeta::create([
                        'post_id' => $carts[$i]->post_id,
                        'user_id' => $carts[$i]->user_id,
                        'status' => -33,
                        'pay_id' => $pay->id,
                        'price'=>$carts[$i]->price,
                        'count' => $carts[$i]->count,
                        'color' => $carts[$i]->color,
                        'size' => $carts[$i]->size,
                    ]);
                    $payMeta->guarantee()->sync($carts[$i]->guarantee_id);
                }
                return response()->json(['pay' => $pay], 200);
            }
        }
    }

    public function successPayNextpay(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $MerchantID = Setting::where('key' , 'nextpay')->pluck('value')->first();
            $user = User::where('email' , $request->user)->first();
            $cart = $user->cart;
            if($cart[0]->delivery && $cart[0]->carrier) {
                $count = Cart::where('user_id' , $user->id)->with('guarantee','carrier')->get();
                for ($i = 0; $i < count($count); $i++) {
                    $countCheck = Post::where('id', $count[$i]->post_id)->pluck('count')->first();
                    $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                    $postSize = [];
                    $postColor = [];
                    $myScore = Score::latest()->where('type' , 0)->where('user_id' , $user->id)->pluck('name')->sum();
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
                };
                $sends1 = $count[0]['carrier'][0];
                if($sends1['limit'] <= $Amount){
                    $sends = 0;
                }else{
                    $sends = $count[0]['carrier'][0]['price'];
                }
                $Amount = (int)$Amount + (int)$sends;
                $carts = Cart::where('user_id', $user->id)->with('guarantee')->get();

                $client = new Client();
                $response = $client->request('POST', 'https://nextpay.org/nx/gateway/verify',
                    [
                        'form_params' => [
                            'api_key' => $MerchantID,
                            'amount' => $Amount,
                            'trans_id' => $request->authority,
                        ],
                        'allow_redirects' => true
                    ]);

                $contents = $response->getBody()->getContents();
                $contents = json_decode($contents, true);


                if ($contents['code'] == 0) {
                    $chars = '012345678901234567890123456789';
                    $chars2 = substr(str_shuffle($chars), 0, 10);
                    if (Pay::where('property', $chars2)->first()) {
                        $chars2 = substr(str_shuffle($chars), 0, 10);
                        if (Pay::where('property', $chars2)->first()) {
                            $chars2 = substr(str_shuffle($chars), 0, 10);
                        }
                    }
                    $pay = Pay::create([
                        'refId' => $contents['Shaparak_Ref_Id'],
                        'time' => $count[0]->delivery,
                        'status' => 100,
                        'property' => $chars2,
                        'price' => $Amount,
                        'user_id' => $user->id,
                        'auth' => $request->authority,
                    ]);
                    for ($i = 0; $i < count($carts); $i++) {
                        $count2 = Post::where('id', $carts[$i]->post_id)->pluck('count')->first();
                        Post::where('id', $carts[$i]->post_id)->first()->update([
                            'count' => $count2 - $carts[$i]->count
                        ]);
                        $payMeta = PayMeta::create([
                            'post_id' => $carts[$i]->post_id,
                            'user_id' => $carts[$i]->user_id,
                            'pay_id' => $pay->id,
                            'price'=>$carts[$i]->price,
                            'status' => 100,
                            'count' => $carts[$i]->count,
                            'color' => $carts[$i]->color,
                            'size' => $carts[$i]->size,
                        ]);
                        $payMeta->guarantee()->sync($carts[$i]->guarantee_id);

                        $post = Post::where('id', $carts[$i]->post_id)->with('review')->first();
                        if ($carts[$i]['color'] != '[]') {
                            $cartColor = json_decode($carts[$i]['color'], true)['name'];
                            $colors = [];
                            foreach (json_decode($post['review'][0]['colors'], true) as $item) {
                                if ($item['name'] == $cartColor) {
                                    $item['count'] = (int)$item['count'] - (int)$carts[$i]['count'];
                                }
                                array_push($colors, $item);
                            }
                            $post->review()->first()->update([
                                'colors' => json_encode($colors),
                            ]);
                        }
                        if ($carts[$i]['size'] != '[]') {
                            $cartSize = json_decode($carts[$i]['size'], true)['name'];
                            $sizes = [];
                            foreach (json_decode($post['review'][0]['size'], true) as $item) {
                                if ($item['name'] == $cartSize) {
                                    $item['count'] = (int)$item['count'] - (int)$carts[$i]['count'];
                                }
                                array_push($sizes, $item);
                            }
                            $post->review()->first()->update([
                                'size' => json_encode($sizes),
                            ]);
                        }
                    }
                    $user->cart()->delete();
                    $address = Setting::where('key' , 'address')->pluck('value')->first();
                    $link = $address."show-pay/$pay->property";
                    if($user->email){
                        $text2 = "<strong>سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به آدرس زیر برین : </strong><br/> <a href='$link'>پیگیری پرداختی</a>";
                        $this->sendEmail($user->email , $text2 , 'خرید موفقیت آمیز');
                    }
                    if($user->number){
                        $text2 = "سلام و درود خدمت شما دوست عزیز ممنون از خریدتان برای پیگیری خرید به پنل کاربریتان مراجعه کنید";
                        $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                        if($sms == 1){
                            $username = '';
                            $password = '';
                            $api = new MelipayamakApi($username,$password);
                            $sms = $api->sms();
                            $to = $user->number;
                            $from = '';
                            $text = $text2;
                            $response = $sms->send($to,$from,$text);
                        }else{
                            $this->sendSms($user->number , $text2,env('GHASEDAKAPI_Number'));
                        }
                    }
                    if($score >= 1){
                        Score::create([
                            'name'=>$score,
                            'user_id'=>$user->id,
                        ]);
                    }
                    return response()->json(['pay' => $pay], 200);
                } else {
                    $chars = '012345678901234567890123456789';
                    $chars2 = substr(str_shuffle($chars), 0, 10);
                    if (Pay::where('property', $chars2)->first()) {
                        $chars2 = substr(str_shuffle($chars), 0, 10);
                        if (Pay::where('property', $chars2)->first()) {
                            $chars2 = substr(str_shuffle($chars), 0, 10);
                        }
                    }
                    $pay = Pay::create([
                        'refId' => '',
                        'status' => $contents['code'],
                        'property' => $chars2,
                        'user_id' => $user->id,
                        'price' => $Amount,
                        'auth' => $request->authority,
                    ]);
                    for ($i = 0; $i < count($carts); $i++) {
                        $payMeta = PayMeta::create([
                            'post_id' => $carts[$i]->post_id,
                            'user_id' => $carts[$i]->user_id,
                            'status' => $contents['code'],
                            'pay_id' => $pay->id,
                            'price'=>$carts[$i]->price,
                            'count' => $carts[$i]->count,
                            'color' => $carts[$i]->color,
                            'size' => $carts[$i]->size,
                        ]);
                        $payMeta->guarantee()->sync($carts[$i]->guarantee_id);
                    }
                    return response()->json(['pay' => $pay], 100);
                }
            }
        }
    }

    public function failPayNextpay(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->user)->first();
            $cart = $user->cart;
            if($cart[0]->delivery && $cart[0]->carrier) {
                $count = Cart::where('user_id' , $user->id)->with('guarantee','carrier')->get();
                for ($i = 0; $i < count($count); $i++) {
                    $countCheck = Post::where('id', $count[$i]->post_id)->pluck('count')->first();
                    $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                    $postSize = [];
                    $postColor = [];
                    $myScore = Score::latest()->where('type' , 0)->where('user_id' , $user->id)->pluck('name')->sum();
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
                };
                $sends1 = $count[0]['carrier'][0];
                if($sends1['limit'] <= $Amount){
                    $sends = 0;
                }else{
                    $sends = $count[0]['carrier'][0]['price'];
                }
                $Amount = (int)$Amount + (int)$sends;
                $carts = Cart::where('user_id', $user->id)->with('guarantee')->get();
                $chars = '012345678901234567890123456789';
                $chars2 = substr(str_shuffle($chars), 0, 10);
                if (Pay::where('property', $chars2)->first()) {
                    $chars2 = substr(str_shuffle($chars), 0, 10);
                    if (Pay::where('property', $chars2)->first()) {
                        $chars2 = substr(str_shuffle($chars), 0, 10);
                    }
                }
                $pay = Pay::create([
                    'refId' => '',
                    'status' => -33,
                    'property' => $chars2,
                    'user_id' => $user->id,
                    'price' => $Amount,
                    'auth' => $request->authority,
                ]);
                for ($i = 0; $i < count($carts); $i++) {
                    $payMeta = PayMeta::create([
                        'post_id' => $carts[$i]->post_id,
                        'user_id' => $carts[$i]->user_id,
                        'status' => -33,
                        'pay_id' => $pay->id,
                        'price'=>$carts[$i]->price,
                        'count' => $carts[$i]->count,
                        'color' => $carts[$i]->color,
                        'size' => $carts[$i]->size,
                    ]);
                    $payMeta->guarantee()->sync($carts[$i]->guarantee_id);
                }
                return response()->json(['pay' => $pay], 200);
            }
        }
    }

    public function getPay(Request $request){
        $token = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $check = $request->header('authorization');
        if($check == $token){
            $user = User::where('email' , $request->user)->first();
            $pay = Pay::latest()->where('user_id' , $user->id)->with('user')->first();
            return response()->json(['pay' => $pay] , 200);
        }
    }
}
