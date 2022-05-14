<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Event;
use App\Models\News;
use App\Models\PayMeta;
use App\Models\Post;
use App\Models\Rank;
use App\Models\Report;
use App\Models\Score;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vidget;
use App\Traits\SeoHelper;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;

class SingleController extends Controller
{
    use SeoHelper;
    public function single(Post $post){
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $name = Setting::where('key' , 'name')->pluck('value')->first();
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $this->seoSingleSeo(   $post->title . " - $title " , $post->body , 'store' , 'product/'."$post->slug" , json_decode($post->image)[0] );

        $time = Carbon::now()->format('Y-m-d h:i');
        DB::table('posts')->where('suggest', '<=' , $time)->update(['suggest'=> null]);
        if(auth()->user()){
            auth()->user()->update([
                'activity' => Verta::now()->format('H:i Y-n-j')
            ]);
        }

        $approve = Setting::where('key' , 'approved')->pluck('value')->first();
        if ($approve == 0){
            $posts = Post::where('id' , $post->id)->with(["post" => function($q){
                $q->where('status' ,1)->with('user','review','guarantee');
            }])->with('tag' , 'user' , 'guarantee' , 'review' , 'category' , 'brand')->with(["question" => function($q){
                $q->where('parent_id' , 0)->where('approved', '=', 1)->with(["questions" => function($q){
                    $q->where('approved', '=', 1);
                }]);
            }])->with(["comments" => function($q){
                $q->where('parent_id' , 0);
            }])->first();
        }else{
            $posts = Post::where('id' , $post->id)->with(["post" => function($q){
                $q->where('status' ,1)->with('user','review','guarantee');
            }])->with('tag' , 'user' , 'guarantee' , 'review' , 'category' , 'brand')->with(["question" => function($q){
                $q->where('parent_id' , 0)->where('approved', '=', 1)->with(["questions" => function($q){
                    $q->where('approved', '=', 1);
                }]);
            }])->with(["comments" => function($q){
                $q->where('parent_id' , 0)->where('approved', '=', 1);
            }])->first();
        }

        $showPostCategory = Setting::where('key' , 'showPostCategory')->pluck('value')->first();

        $related =  Post::whereHas('category', function ($q) use ($post){
            return $q->whereIn('name', $post->category()->pluck('name'));
        })->where('id' , '!=' , $post->id)->where('type' , 0)->where('status' , 1)->take($showPostCategory)->get();

        $show1 = Setting::where('key' , 'checkUser')->pluck('value')->first();
        if ($show1 == 1){
            if (auth()->user()){
                $show = true;
            }else{
                $show = false;
            }
        }else{
            $show = true;
        }

        if (auth()->user()){
            $feedback1 = Report::where('user_id' , auth()->user()->id)->where('reportable_id', $post->id)->where('reportable_type', 'App\\Models\\Post')->where('type' , 1)->first();
            $notification1 = Report::where('user_id' , auth()->user()->id)->where('reportable_id', $post->id)->where('reportable_type', 'App\\Models\\Post')->where('type' , 2)->first();
            if($feedback1){
                $feedback = 1;
            }else{
                $feedback = 0;
            }
            if($notification1){
                $notification = 1;
            }else{
                $notification = 0;
            }
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
        }else{
            $feedback = 0;
            $notification= 0;
            $finalPrice = $post->price;
        }

        $reply = Setting::where('key' , 'reply')->pluck('value')->first();
        $coercion = Setting::where('key' , 'coercion')->pluck('value')->first();
        $showUser = Setting::where('key' , 'showUser')->pluck('value')->first();
        $checkOnline = Setting::where('key' , 'checkOnline')->pluck('value')->first();
        $vidgets = Vidget::where('platform' , 2)->get();
        return Inertia::render('Single/SingleIndex' , [
            'address' => $address,
            'title' => $title,
            'vidgets' => $vidgets,
            'name' => $name,
            'notification' => $notification,
            'finalPrice' => $finalPrice,
            'feedback' => $feedback,
            'reply' => $reply,
            'coercion' => $coercion,
            'showUser' => $showUser,
            'checkOnline' => $checkOnline,
            'show' => $show,
            'posts' => $posts,
            'related' => $related,
        ]);
    }
    public function downloadProduct(Post $post){
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $name = Setting::where('key' , 'name')->pluck('value')->first();
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $this->seoSingleSeo(   $post->title . " - $title " , $post->body , 'store' , 'product/'."$post->slug" , json_decode($post->image)[0] );

        $time = Carbon::now()->format('Y-m-d h:i');
        DB::table('posts')->where('suggest', '<=' , $time)->update(['suggest'=> null]);
        if(auth()->user()){
            auth()->user()->update([
                'activity' => Verta::now()->format('H:i Y-n-j')
            ]);
        }

        $approve = Setting::where('key' , 'approved')->pluck('value')->first();
        if ($approve == 0){
            $posts = Post::where('id' , $post->id)->with('tag' , 'user' ,'category' , 'review')->withCount(["payMeta" => function($q){
                $q->where('status', '=', 100);
            }])->withCount(["question" => function($q){
                $q->where('approved', '=', 1);
            }])->with(["question" => function($q){
                $q->where('parent_id' , 0)->where('approved', '=', 1)->with(["questions" => function($q){
                    $q->where('approved', '=', 1);
                }]);
            }])->withCount('comments')->with(["comments" => function($q){
                $q->where('parent_id' , 0);
            }])->first();
        }else{
            $posts = Post::where('id' , $post->id)->with('tag' , 'user' , 'review' , 'category')->withCount(["payMeta" => function($q){
                $q->where('status', '=', 100);
            }])->withCount(["question" => function($q){
                $q->where('approved', '=', 1);
            }])->with(["question" => function($q){
                $q->where('parent_id' , 0)->where('approved', '=', 1)->with(["questions" => function($q){
                    $q->where('approved', '=', 1);
                }]);
            }])->withCount(["comments" => function($q){
                $q->where('approved', '=', 1);
            }])->with(["comments" => function($q){
                $q->where('parent_id' , 0)->where('approved', '=', 1);
            }])->first();
        }

        $showPostCategory = Setting::where('key' , 'showPostCategory')->pluck('value')->first();

        $related =  Post::whereHas('category', function ($q) use ($post){
            return $q->whereIn('name', $post->category()->pluck('name'));
        })->where('id' , '!=' , $post->id)->where('type' , 1)->with('category')->where('status' , 1)->take($showPostCategory)->get();

        $show1 = Setting::where('key' , 'checkUser')->pluck('value')->first();
        if (auth()->user()){
            if ($show1 == 1){
                $show = true;
            }else{
                $show = false;
            }
            $paid1 = PayMeta::where('status' , 100)->where('user_id' , auth()->user()->id)->where('post_id' , $post->id)->first();
            if ($paid1){
                $paid = true;
            }else{
                $paid = false;
            }
        }else{
            $show = false;
            $paid = false;
        }
        $reply = Setting::where('key' , 'reply')->pluck('value')->first();
        $coercion = Setting::where('key' , 'coercion')->pluck('value')->first();
        $showUser = Setting::where('key' , 'showUser')->pluck('value')->first();
        $checkOnline = Setting::where('key' , 'checkOnline')->pluck('value')->first();
        $vidgets = Vidget::where('platform' , 2)->get();
        return Inertia::render('Single/SingleDownload' , [
            'address' => $address,
            'title' => $title,
            'vidgets' => $vidgets,
            'name' => $name,
            'reply' => $reply,
            'coercion' => $coercion,
            'showUser' => $showUser,
            'checkOnline' => $checkOnline,
            'show' => $show,
            'paid' => $paid,
            'posts' => $posts,
            'related' => $related,
        ]);
    }
    public function news(News $news){
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $name = Setting::where('key' , 'name')->pluck('value')->first();
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $this->seoSingleSeo(   $news->title . " - $title " , $news->body , 'store' , 'news/'."$news->slug" , $news->image );

        $related =  News::whereHas('category', function ($q) use ($news){
            return $q->whereIn('name', $news->category()->pluck('name'));
        })->where('id' , '!=' , $news->id)->where('status' , 1)->take(6)->get();
        $suggest = News::where('suggest',1)->where('status',1)->latest()->get();
        $post = News::where('id',$news->id)->with('user','category','tag')->first();
        return Inertia::render('Single/SingleNews', [
            'related' => $related,
            'suggest' => $suggest,
            'post' => $post,
        ]);
    }
    public function download(Post $post){
        $access = PayMeta::where('user_id',auth()->user()->id)->where('status' , 100)->where('post_id',$post->id)->first();
        if($access){
            return Storage::disk('download')->download($post['file']);
        }else{
            return abort(404);
        }
    }
}
