<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\News;
use App\Models\Page;
use App\Models\Pay;
use App\Models\Post;
use App\Models\Rank;
use App\Models\Robot;
use App\Models\Score;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\Vidget;
use App\Models\View;
use App\Traits\SeoHelper;
use Carbon\Carbon;
use App\Models\User;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Inertia\Inertia;

class IndexController extends Controller
{
    use SeoHelper;
    public function index(Request $request){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $this->setIndexSeo();
        if (auth()->user()){
            $robots = Robot::where('user_id' , auth()->user()->id)->where('status' , 1)->pluck('id');
        }else{
            $robots = [];
        }
        $time = Carbon::now()->format('Y-m-d h:i');
        DB::table('posts')->where('suggest', '<=' , $time)->update(['suggest'=> null]);
        $vidgets = Vidget::where('platform' , 0)->get();
        $vidget = [];
        foreach ($vidgets as $item){
            $vidgetCategory = [
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
            if($item['category'] != '' && $item['name'] != 'خبر ها' && $item['name'] != 'تبلیغات ساده' && $item['name'] != 'اسلایدر بزرگ تبلیغ' && $item['name'] != 'تبلیغات اسلایدری'){
                $allCatSite3 = explode(',' , $item['category']);
                foreach ($allCatSite3 as $value){
                    $tax = Category::where('name' , $value)->first();
                    if($tax){
                        $send2 = $tax->post()->pluck('id')->toArray();
                        foreach ($send2 as $data){
                            array_push($ids ,$data);
                        }
                    }
                }
            }
            if($item['brand'] != '' && $item['name'] != 'خبر ها' && $item['name'] != 'تبلیغات ساده' && $item['name'] != 'پیشنهاد شگفت انگیز' && $item['name'] != 'اسلایدر بزرگ تبلیغ' && $item['name'] != 'تبلیغات اسلایدری'){
                $allBrandSite3 = explode(',' , $item['brand']);
                foreach ($allBrandSite3 as $value){
                    $tax = Brand::where('name' , $value)->first();
                    if($tax){
                        $send2 = $tax->post()->pluck('id')->toArray();
                        foreach ($send2 as $data){
                            array_push($ids2 ,$data);
                        }
                    }
                }
            }
            if($item['category'] == '' && $item['name'] != 'ویژگی ها'){
                $ids = Post::latest()->where('variety' , 0)->pluck('id')->take(500)->toArray();
            }
            if($item['brand'] == '' && $item['name'] != 'ویژگی ها' || $item['name'] == 'پیشنهاد شگفت انگیز'){
                $ids2 = Post::latest()->where('variety' , 0)->pluck('id')->take(500)->toArray();
            }
            $arrayFilter = array_intersect($ids2, $ids);
            if ($item['show'] == 0){
                if($item['type'] == 3){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('type' , 0)->where('variety' , 0)->latest()->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 0){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->latest()->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 1){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->latest()->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 2){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->latest()->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
            }
            if ($item['show'] == 1 or $item['show'] == 2){
                if($item['type'] == 3){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->withCount('view')->orderBy('view_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 0){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->withCount('view')->orderBy('view_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 1){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->withCount('view')->orderBy('view_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 2){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->withCount('view')->orderBy('view_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
            }
            if ($item['show'] == 3){
                if($item['type'] == 3){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('variety' , 0)->orderBy('price')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 0){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->orderBy('price')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 1){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->orderBy('price')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 2){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->orderBy('price')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
            }
            if ($item['show'] == 4){
                if($item['type'] == 3){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->orderBy('price','DESC')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 0){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->orderBy('price','DESC')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 1){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->orderBy('price','DESC')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 2){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->orderBy('price','DESC')->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
            }
            if ($item['show'] == 5){
                if($item['type'] == 3){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 0){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 1){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
                }
                if($item['type'] == 2){
                    $catPost1 = Post::whereIn('id' , $arrayFilter)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->where('status' , 1)->take($item['count'])->with('review')->with(["payMeta" => function($q){
                        $q->where('status' ,100);
                    }])->get();
                    $catPost = ProductResource::collection($catPost1);
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
            if($item['name'] == 'خبر ها'){
                $vidgetCategory['post'] = [];
                if($item['category'] != ''){
                    $allCatSite3 = explode(',' , $item['category']);
                    foreach ($allCatSite3 as $value){
                        $tax = Category::where('name' , $value)->first();
                        $send2 = $tax->news()->pluck('id')->toArray();
                        foreach ($send2 as $data){
                            array_push($ids ,$data);
                        }
                    }
                }else{
                    $ids = News::pluck('id')->toArray();
                }
                $catPost = News::latest()->whereIn('id' , $ids)->where('status' , 1)->take($item['count'])->with('category','user')->get();
                $vidgetCategory['post'] = $catPost;
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
            if($item['name'] == 'محصولات دانلودی'){
                $vidgetCategory['post'] = [];
                $vidgetCategory['post'] = Post::where('status' , 1)->with('category')->where('type' , 1)->take($item['count'])->get();
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
        $moment1 = Post::inRandomOrder()->where('status' , 1)->where('variety' , 0)->where('type' , 0)->where('count' , '>=' , 1)->where('off' , null)->take(20)->get();
        $moment = ProductResource::collection($moment1);
        $maxPrice = Post::orderBy('price','DESC')->where('count' , '>=' , 1)->where('type' , 0)->pluck('price')->first();
        $minPrice = Post::orderBy('price')->where('count' , '>=' , 1)->where('type' , 0)->pluck('price')->first();
        return Inertia::render('Index/IndexHome' , [
            'title' => $title,
            'robots' => $robots,
            'moment' => $moment,
            'vidget' => $vidget,
            'maxPrice' => $maxPrice,
            'minPrice' => $minPrice,
        ]);
    }
    public function getSugest(Request $request){
        return Post::where('id' , $request->postSuggestId)->where('count' , '>=' , 1)->where('variety' , 0)->with('review')->first();
    }
    public function showFast(Request $request){
        return Post::where('id' , $request->postId)->with('review','category','brand')->where('variety' , 0)->first();
    }
    public function showCompares(Request $request){
        $postsItem =[];
        foreach ($request->postCompare as $posts) {
            $send = Post::where('id' , $posts)->with('review')->first();
            array_push($postsItem ,$send);
        }
        return $postsItem;
    }
    public function sendRobot(Robot $robot){
        if(auth()->user()){
            $addressSite = Setting::where('key' , 'address')->pluck('value')->first();
            $bot = Robot::where('id' , $robot->id)->where('status' , 1)->where('user_id' , auth()->user()->id)->first();
            if ($bot){
                $text1 = '';
                $text2 = '';
                $text3 = '';
                $text4 = '';
                $text5 = '';
                $text6 = '';
                $text7 = '';
                $text8 = '';
                $text9 = '';
                $text10 = '';
                $text11 = '';
                $text12 = '';
                foreach(json_decode($bot->data) as $list){
                    if($list == 'payDay'){
                        $pays =number_format(Pay::where('status' , 100)->whereDate('created_at', Carbon::today())->pluck('price')->sum());
                        $address = $addressSite . 'admin/pay';
                        $text1 = urlencode("<strong>پرداختی های امروز : </strong>\n $pays تومان\n<a href='$address'>مشاهده همه</a>\n\n");
                    }
                    if($list == 'payYesterDay'){
                        $pays =number_format(Pay::where('status' , 100)->whereDate('created_at', Carbon::yesterday())->pluck('price')->sum());
                        $address = $addressSite . 'admin/pay';
                        $text2 = urlencode("<strong>پرداختی های دیروز : </strong>\n $pays تومان\n<a href='$address'>مشاهده همه</a>\n\n");
                    }
                    if($list == 'payMonth'){
                        $pays =number_format(Pay::where('status' , 100)->whereMonth('created_at', Carbon::now()->month)->pluck('price')->sum());
                        $address = $addressSite . 'admin/pay';
                        $text3 = urlencode("<strong>پرداختی های این ماه : </strong>\n $pays تومان\n<a href='$address'>مشاهده همه</a>\n\n");
                    }
                    if($list == 'ticketDay'){
                        $tickets =number_format(Ticket::whereDate('created_at', Carbon::today())->count());
                        $address = $addressSite . 'admin/ticket';
                        $text4 = urlencode("<strong>تیکت های امروز : </strong>\n $tickets تیکت\n<a href='$address'>مشاهده همه</a>\n\n");
                    }
                    if($list == 'ticketYesterDay'){
                        $tickets =number_format(Ticket::whereDate('created_at', Carbon::yesterday())->count());
                        $address = $addressSite . 'admin/ticket';
                        $text5 = urlencode("<strong>تیکت های دیروز : </strong>\n $tickets تیکت\n<a href='$address'>مشاهده همه</a>\n\n");
                    }
                    if($list == 'ticketMonth'){
                        $tickets =number_format(Ticket::whereMonth('created_at', Carbon::now()->month)->count());
                        $address = $addressSite . 'admin/ticket';
                        $text6 = urlencode("<strong>تیکت های این ماه : </strong>\n $tickets تیکت\n<a href='$address'>مشاهده همه</a>\n\n");
                    }
                    if($list == 'viewDay'){
                        $views =number_format(View::whereDate('created_at', Carbon::today())->count());
                        $address = $addressSite . 'admin/view';
                        $text7 = urlencode("<strong>بازدید های امروز : </strong>\n $views بازدید\n<a href='$address'>مشاهده همه</a>\n\n");
                    }
                    if($list == 'viewYesterDay'){
                        $views =number_format(View::whereDate('created_at', Carbon::yesterday())->count());
                        $address = $addressSite . 'admin/view';
                        $text8 = urlencode("<strong>بازدید های دیروز : </strong>\n $views بازدید\n<a href='$address'>مشاهده همه</a>\n\n");
                    }
                    if($list == 'viewMonth'){
                        $views =number_format(View::whereMonth('created_at', Carbon::now()->month)->count());
                        $address = $addressSite . 'admin/view';
                        $text9 = urlencode("<strong>بازدید های این ماه : </strong>\n $views بازدید\n<a href='$address'>مشاهده همه</a>\n\n");
                    }
                    if($list == 'registerDay'){
                        $views =number_format(User::whereDate('created_at', Carbon::today())->count());
                        $address = $addressSite . 'admin/user';
                        $text10 = urlencode("<strong>ثبت نام امروز : </strong>\n $views کاربر\n<a href='$address'>مشاهده همه</a>\n\n");
                    }
                    if($list == 'registerYesterDay'){
                        $views =number_format(User::whereDate('created_at', Carbon::yesterday())->count());
                        $address = $addressSite . 'admin/user';
                        $text11 = urlencode("<strong>ثبت نام دیروز : </strong>\n $views کاربر\n<a href='$address'>مشاهده همه</a>\n\n");
                    }
                    if($list == 'registerMonth'){
                        $views =number_format(User::whereMonth('created_at', Carbon::now()->month)->count());
                        $address = $addressSite . 'admin/user';
                        $text12 = urlencode("<strong>ثبت نام این ماه : </strong>\n $views کاربر\n<a href='$address'>مشاهده همه</a>\n\n");
                    }
                }
                $texts = $text1 . $text2 . $text3 . $text4 . $text5 . $text6 . $text7 . $text8 . $text9 . $text10 . $text11 . $text12;
                return redirect("https://api.telegram.org/bot".$bot->token."/sendMessage?chat_id=@".$bot->group."&text=".$texts."&parse_mode=html");
            }else{
                return 'notFound';
            }
        }
        return 'noUser';
    }
    public function page(Page $page){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first() ?:'' ;
        $map = Setting::where('key' , 'map')->pluck('value')->first() ?:'' ;
        $this->seoSingleSeo(   $page->title . " - $title " , $page->body , 'store' , "$page->slug" , $logo );
        return Inertia::render('Page/PageIndex' , [
            'page' => $page,
            'title' => $title,
            'map' => $map,
        ]);
    }
}
