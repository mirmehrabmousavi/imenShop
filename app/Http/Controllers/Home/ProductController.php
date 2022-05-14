<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Guarantee;
use App\Models\PayMeta;
use App\Models\Post;
use App\Models\Report;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Traits\SendEmailTrait;
use App\Traits\SendSmsTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request){
        if ($request->getPage){
            $page = $request->getPage;
        }else{
            $page = '25';
        }

        if ($request->category){
            $categoryName = auth()->user()->category()->latest()->where('name' , $request->category)->first();
            $category = $categoryName->post()->pluck('id')->toArray();
        }else{
            $category = auth()->user()->post()->latest()->pluck('id')->toArray();
        }

        $categories = Category::latest()->get();
        if ($request->sort == 1){
            if ($request->search){
                if ($request->date){
                    $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    if (count($posts) == 0){
                        $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                }else{
                    $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    if (count($posts) == 0){
                        $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                }
            }else{
                if ($request->date){
                    $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereDate('created_at',$request->date)->whereIn('id',$category)->paginate($page);
                }else{
                    $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->paginate($page);
                }
            }
        }elseif($request->sort == 2){
            if ($request->search){
                if ($request->date){
                    $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    if (count($posts) == 0){
                        $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                }else{
                    $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    if (count($posts) == 0){
                        $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                }
            }else{
                if ($request->date){
                    $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereDate('created_at',$request->date)->whereIn('id',$category)->paginate($page);
                }else{
                    $posts = auth()->user()->post()->latest()->where('variety' , 0)->where('status' , '1')->whereIn('id',$category)->paginate($page);
                }
            }
        }else{
            if ($request->search){
                if ($request->date){
                    $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    if (count($posts) == 0){
                        $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                }else{
                    $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    if (count($posts) == 0){
                        $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                }
            }else{
                if ($request->date){
                    $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id',$category)->whereDate('created_at',$request->date)->paginate($page);
                }else{
                    $posts = auth()->user()->post()->latest()->where('variety' , 0)->whereIn('id', $category)->paginate($page);
                }
            }
        }
        return Inertia::render('Home/User/AllPost' , [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    public function allProducts(Request $request){
        if ($request->search){
            $posts = Post::latest()->where("title" , "LIKE" , "%{$request->search}%")->with('category')->withCount(["post" => function($q){
                $q->where('status' ,1);
            }])->where('variety',0)->paginate(60);
        }else{
            $posts = Post::latest()->with('category')->withCount(["post" => function($q){
                $q->where('status' ,1);
            }])->where('variety',0)->paginate(60);
        }
        return Inertia::render('Home/User/AllProducts' , [
            'posts' => $posts,
        ]);
    }

    public function addVariety(Request $request , Post $post){
        $posts = Post::where('id' , $post->id)->with('review','category','guarantee')->with(["post" => function($q){
            $q->where('status' ,1)->with('user','review','guarantee');
        }])->first();
        if($request->update){
            $request->validate([
                'count' => 'required',
                'price' => 'required',
            ]);
            if ($request->off){
                $price = round($request->price - $request->price * $request->off / 100);
            }else{
                $price = $request->price;
            }
            $productIds = Post::buildCode();
            $productId = Setting::where('key', 'productId')->pluck('value')->first();
            $variety = Post::create([
                'body' => $posts->summery,
                'bodyEn' => $posts->summeryEn,
                'count' => $request->count,
                'title' => $posts->title,
                'titleEn' => $posts->titleEn,
                'showcase' => 0,
                'used' => $posts->used,
                'original' => $posts->original,
                'status' => 0,
                'variety' => 1,
                'slug' => $posts->slug,
                'image' => $posts->image,
                'price' => $price,
                'offPrice' => $request->price,
                'off' => $request->off,
                'suggest' => null,
                'user_id' => auth()->user()->id,
                'product_id' => $productId . '-' . $productIds,
            ]);
            $meta = Review::create([
                'body' => $posts['review'][0]['body'],
                'bodyEn' => $posts['review'][0]['bodyEn'],
                'rate' => $posts['review'][0]['allRate'],
                'ability' => $posts['review'][0]['allAbility'],
                'size' => $request->allSize,
                'specifications' => $posts['review'][0]['allProperty'],
                'colors' => $request->allColor,
            ]);
            $variety->review()->sync($meta->id);
            $variety->guarantee()->sync($request->allGuarantee);
            $post->post()->attach($variety->id);
            return redirect('/profile/all-products');
        }
        $guarantees = Guarantee::latest()->pluck('name', 'id');
        return Inertia::render('Home/User/AddVariety' , [
            'posts' => $posts,
            'guarantees' => $guarantees,
        ]);
    }

    public function create(Request $request)
    {
        $productId = Setting::where('key', 'productId')->pluck('value')->first();
        if($request->title or $request->image or $request->body or $request->summery){
            $request->validate([
                'title' => 'required|max:220',
                'image' => 'required',
                'status' => 'required',
                'count' => 'required',
                'price' => 'required',
            ]);
            if ($request->off){
                $price = round($request->price - $request->price * $request->off / 100);
            }else{
                $price = $request->price;
            }
            $productIds = Post::buildCode();
            $post = Post::create([
                'body' => $request->summery,
                'bodyEn' => $request->summeryEn,
                'count' => $request->count,
                'title' => $request->title,
                'titleEn' => $request->titleEn,
                'used' => $request->used,
                'original' => $request->original,
                'image' => $request->image,
                'price' => $price,
                'offPrice' => $request->price,
                'off' => $request->off,
                'user_id' => auth()->user()->id,
                'product_id' => $productId . '-' . $productIds,
            ]);
            $meta = Review::create([
                'body' => $request->body,
                'bodyEn' => $request->bodyEn,
                'rate' => $request->allRate,
                'ability' => $request->allAbility,
                'size' => $request->allSize,
                'specifications' => $request->allProperty,
                'colors' => $request->allColor,
            ]);
            $post->review()->sync($meta->id);
            $post->category()->sync($request->allCategory);
            $post->brand()->sync($request->allBrand);
            $post->guarantee()->sync($request->allGuarantee);
        }
        $categories = Category::latest()->pluck('name', 'id');
        $brands = Brand::latest()->pluck('name', 'id');
        $guarantees = Guarantee::latest()->pluck('name', 'id');
        return Inertia::render('Home/User/PostCreate', [
            'categories' => $categories,
            'brands' => $brands,
            'guarantees' => $guarantees,
        ]);
    }

    public function edit(Post $post , Request $request){
        if($request->title or $request->image or $request->body or $request->summery){
            $request->validate([
                'title' => 'required',
                'count' => 'required',
                'image' => 'required',
                'price' => 'required',
            ]);

            if ($request->off){
                $price = round($request->price - $request->price * $request->off / 100);
            }else{
                $price = $request->price;
            }
            $post->update([
                'body' => $request->summery,
                'bodyEn' => $request->summeryEn,
                'count' => $request->count,
                'title' => $request->title,
                'titleEn' => $request->titleEn,
                'used' => $request->used,
                'original' => $request->original,
                'slug' => $request->slug,
                'image' => $request->image,
                'price' => $price,
                'offPrice' => $request->price,
                'off' => $request->off,
                'updated_at'=> Carbon::now(),
            ]);
            $post->review()->first()->update([
                'body' => $request->body,
                'bodyEn' => $request->bodyEn,
                'rate' => $request->allRate,
                'ability' => $request->allAbility,
                'size' => $request->allSize,
                'specifications' => $request->allProperty,
                'colors' => $request->allColor,
            ]);
            $post->category()->detach();
            $post->brand()->detach();
            $post->guarantee()->detach();
            $post->category()->sync($request->allCategory);
            $post->brand()->sync($request->allBrand);
            $post->guarantee()->sync($request->allGuarantee);
        }
        $categories = Category::latest()->pluck('name' , 'id');
        $brands = Brand::latest()->pluck('name' , 'id');
        $guarantees = Guarantee::latest()->pluck('name' , 'id');
        $posts = Post::where('id' , $post->id)->with('review','guarantee','category','brand')->first();
        return Inertia::render('Home/User/EditPost', [
            'categories' => $categories,
            'posts' => $posts,
            'brands' => $brands,
            'guarantees' => $guarantees,
        ]);
    }

    public function show(Post $post)
    {
        $payMeta = PayMeta::where('post_id' , $post->id)->where('status' , 100)->with('user','pay')->paginate(20);
        $reply = Setting::where('key' , 'replyComment')->pluck('value')->first();
        $coercion = Setting::where('key' , 'coercionComment')->pluck('value')->first();
        $showUser = Setting::where('key' , 'showUserComment')->pluck('value')->first();
        $checkOnline = Setting::where('key' , 'checkOnlineComment')->pluck('value')->first();
        $posts = Post::where('id',$post->id)->with(["payMeta" => function($q){
            $q->where('status' , 100);
        }])->with('review' , 'user')->withCount('bookmark' , 'like' , 'view' , 'comments')->first();
        $allPay= PayMeta::where('status' , 100)->where('post_id' , $post->id)->pluck('price')->sum();
        return Inertia::render('Home/User/ShowPost' , [
            'allPay' => $allPay,
            'payMeta' => $payMeta,
            'posts' => $posts,
            'reply' => $reply,
            'coercion' => $coercion,
            'showUser' => $showUser,
            'checkOnline' => $checkOnline,
        ]);
    }

    public function pays(Request $request){
        $posts = Post::where('user_id' , auth()->user()->id)->latest()->where('status' , 1)->pluck('id')->toArray();
        $allPays = PayMeta::whereIn('post_id' , $posts)->where('status' , 100)->pluck('price')->sum();
        $payToday = PayMeta::whereDate('created_at',Carbon::today())->whereIn('post_id' , $posts)->where('status' , 100)->pluck('price')->sum();
        $payWeek = PayMeta::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->whereIn('post_id' , $posts)->where('status' , 100)->pluck('price')->sum();
        $pays = PayMeta::latest()->whereIn('post_id' , $posts)->where('status' , 100)->with('post','pay','user')->paginate(25);
        return Inertia::render('Home/User/PayProduct' , [
            'pays' => $pays,
            'allPays' => $allPays,
            'payToday' => $payToday,
            'payWeek' => $payWeek,
        ]);
    }

    public function payShow(PayMeta $payMeta){
        return PayMeta::latest()->where('id' , $payMeta->id)->with('post','pay')->with(["user" => function($q){
            $q->with('userMeta');
        }])->first();
    }

    public function image(Request $request){
        $year = Carbon::now()->year;
        $folder = public_path('upload/document/' . $year . '/' . auth()->user()->id . '/');
        if (!file_exists($folder)){
            mkdir($folder , 0755 , true);
        }
        $file = $request->file;
        $name = $file->getClientOriginalName();
        $type = $file->getClientOriginalExtension();
        $sizefile = $file->getsize()/1000;
        if( $sizefile > 1000){
            $size=round($sizefile/1000 ,2) . 'mb';
        }else{
            $size=round($sizefile) . 'kb';
        }
        if ($type == "jpg" or $type == "JPG" or $type == "png" or $type == "jpeg" or $type == "svg" or $type == "tif" or $type == "gif" or $type == "jfif"){
            $url = "/upload/document/" . $year . '/' . auth()->user()->id;
            $path = $file->move($_SERVER['DOCUMENT_ROOT'] .$url , time() . '.' . $type);
            $gallery = Gallery::create([
                'name' => $name,
                'size' => $size,
                'type' => $type,
                'user_id' => auth()->user()->id,
                'url' => $url . '/' . time() . '.' . $type,
                'path' => $path->getRealPath(),
            ]);
            return $gallery;
        }else{
            return 'format';
        }
    }

    public function createGallery(Request $request){
        $year = Carbon::now()->year;
        $folder = public_path('upload/document/' . $year . '/' . auth()->user()->id . '/');
        if (!file_exists($folder)){
            mkdir($folder , 0755 , true);
        }
        $file = $request->file;
        $name = $file->getClientOriginalName();
        $type = $file->getClientOriginalExtension();
        $sizefile = $file->getsize()/1000;
        if( $sizefile > 1000){
            $size=round($sizefile/1000 ,2) . 'mb';
        }else{
            $size=round($sizefile) . 'kb';
        }
        if ($type == "jpg" or $type == "JPG" or $type == "png" or $type == "jpeg" or $type == "svg" or $type == "tif" or $type == "gif" or $type == "jfif"){
            $url = "/upload/document/" . $year . '/' . auth()->user()->id;
            $path = $file->move($_SERVER['DOCUMENT_ROOT'] .$url , time() . '.' . $type);
            $gallery = Gallery::create([
                'name' => $name,
                'size' => $size,
                'type' => $type,
                'user_id' => auth()->user()->id,
                'url' => $url . '/' . time() . '.' . $type,
                'path' => $path->getRealPath(),
            ]);
            return $gallery;
        }else{
            return 'format';
        }
    }

    public function allGallery(){
        return auth()->user()->gallery()->latest()->where('status', '!=' , 2)->get();
    }
}

