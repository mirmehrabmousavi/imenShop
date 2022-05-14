<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Guarantee;
use App\Models\PayMeta;
use App\Models\Post;
use App\Models\Report;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Time;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VarietyController extends Controller
{
    public function index(Request $request){
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش تنوع')->pluck('name');
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش تنوع')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف تنوع')->pluck('name');
        if(auth()->user()->admin == 1 or count($checkEdits) >= 1){
            $edits = 1;
        }else{
            $edits = 0;
        }
        if(auth()->user()->admin == 1 or count($checkDeletes) >= 1){
            $deletes = 1;
        }else{
            $deletes = 0;
        }
        if(auth()->user()->admin == 1 or count($showSome) >= 1){
            $shows = 1;
        }else{
            $shows = 0;
        }

        if($request->value){
            foreach ($request->value as $value) {
                $post = Post::where('id', $value)->first();
                $post->category()->detach();
                $post->bookmark()->delete();
                $post->comments()->delete();
                $post->like()->delete();
                $post->brand()->detach();
                $post->payMeta()->delete();
                $post->cart()->delete();
                $post->rate()->delete();
                $post->report()->delete();
                $post->question()->delete();
                $post->review()->detach();
                $post->guarantee()->detach();
                Review::where('id' , $post->review()->pluck('id')->first())->delete();
            }
            DB::table('posts')->whereIn('id', $request->value)->delete();
        }

        if ($request->getPage){
            $page = $request->getPage;
        }else{
            $page = '25';
        }

        if ($request->category){
            if (count($showSome) >= 1){
                $categoryName = auth()->user()->category()->latest()->where('name' , $request->category)->first();
                $category = $categoryName->post()->where('variety',1)->pluck('id')->toArray();
            }else{
                $categoryName = Category::latest()->where('name' , $request->category)->first();
                $category = $categoryName->post()->where('variety',1)->pluck('id')->toArray();
            }
        }else{
            if (count($showSome) >= 1){
                $category = auth()->user()->post()->where('variety',1)->latest()->pluck('id')->toArray();
            }else{
                $category = Post::latest()->where('variety',1)->pluck('id')->toArray();
            }
        }

        $categories = Category::latest()->get();
        if ($request->sort == 1){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->where('variety',1)->latest()->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->where('variety',1)->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->where('variety',1)->latest()->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->where('variety',1)->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->where('variety',1)->latest()->where('status' , '0')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->where('variety',1)->where('status' , '0')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->where('variety',1)->latest()->where('status' , '0')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->where('variety',1)->where('status' , '0')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->where('variety',1)->latest()->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '0')->paginate($page);
                    }else{
                        $posts = Post::latest()->where('variety',1)->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '0')->paginate($page);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->where('variety',1)->latest()->where('status' , '0')->whereIn('id',$category)->paginate($page);
                    }else{
                        $posts = Post::latest()->where('variety',1)->where('status' , '0')->whereIn('id',$category)->paginate($page);
                    }
                }
            }
        }elseif($request->sort == 2){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->where('variety',1)->latest()->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->where('variety',1)->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->where('variety',1)->latest()->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->where('variety',1)->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->where('variety',1)->latest()->where('status' , '1')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->where('variety',1)->where('status' , '1')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->where('variety',1)->latest()->where('status' , '1')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->where('variety',1)->where('status' , '1')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->where('variety',1)->latest()->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '1')->paginate($page);
                    }else{
                        $posts = Post::latest()->where('variety',1)->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '1')->paginate($page);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->where('variety',1)->latest()->where('status' , '1')->whereIn('id',$category)->paginate($page);
                    }else{
                        $posts = Post::latest()->where('variety',1)->where('status' , '1')->whereIn('id',$category)->paginate($page);
                    }
                }
            }
        }else{
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->where('variety',1)->latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->where('variety',1)->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->where('variety',1)->latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->where('variety',1)->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->where('variety',1)->latest()->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->where('variety',1)->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->where('variety',1)->latest()->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->where('variety',1)->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->where('variety',1)->latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->paginate($page);
                    }else{
                        $posts = Post::latest()->where('variety',1)->whereIn('id', $category)->whereDate('created_at',$request->date)->paginate($page);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->where('variety',1)->latest()->whereIn('id', $category)->paginate($page);
                    }else{
                        $posts = Post::latest()->where('variety',1)->whereIn('id',$category)->paginate($page);
                    }
                }
            }
        }
        $labels = ['آیدی','تصویر','عنوان','وضعیت','مبلغ','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('AllVariety' , [
            'labels' => $labels,
            'posts' => $posts,
            'edits' => $edits,
            'deletes' => $deletes,
            'shows' => $shows,
            'categories' => $categories,
        ]);
    }

    public function create(Request $request , Post $post)
    {
        $posts = Post::where('id' , $post->id)->with('review','category','guarantee')->with(["post" => function($q){
            $q->where('status' ,1)->with('user','review','guarantee');
        }])->first();
        if($request->update){
            $request->validate([
                'count' => 'required',
                'price' => 'required',
                'status' => 'required',
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
                'status' => $request->status,
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
        }
        $guarantees = Guarantee::latest()->pluck('name', 'id');
        Inertia::setRootView('admin');
        return Inertia::render('CreateVariety' , [
            'posts' => $posts,
            'guarantees' => $guarantees,
        ]);
    }


    public function show(Post $post)
    {
        $users = User::get();
        foreach ($users as $user) {
            if (Cache::has('user-is-online-' . $user->id)) {
                $user->update([
                    'activity' => Verta::now()->format('H:i Y-n-j')
                ]);
            }
        }
        $payMeta = PayMeta::where('post_id' , $post->id)->with('user','pay','guarantee')->paginate(20);
        $reply = Setting::where('key' , 'replyComment')->pluck('value')->first();
        $coercion = Setting::where('key' , 'coercionComment')->pluck('value')->first();
        $showUser = Setting::where('key' , 'showUserComment')->pluck('value')->first();
        $checkOnline = Setting::where('key' , 'checkOnlineComment')->pluck('value')->first();
        $posts = Post::where('id',$post->id)->with('review' , 'user')->withCount('bookmark' , 'review' , 'like' , 'view' , 'payMeta' , 'comments')->first();
        Inertia::setRootView('admin');
        return Inertia::render('ShowPost' , [
            'payMeta' => $payMeta,
            'posts' => $posts,
            'reply' => $reply,
            'coercion' => $coercion,
            'showUser' => $showUser,
            'checkOnline' => $checkOnline,
        ]);
    }


    public function edit(Post $post , Request $request){
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
            $post->update([
                'count' => $request->count,
                'price' => $price,
                'offPrice' => $request->price,
                'off' => $request->off,
                'status' => $request->status,
            ]);
            $post->review()->first()->update([
                'size' => $request->allSize,
                'colors' => $request->allColor,
            ]);
            $post->guarantee()->detach();
            $post->guarantee()->sync($request->allGuarantee);
        }
        $categories = Category::latest()->pluck('name' , 'id');
        $brands = Brand::latest()->pluck('name' , 'id');
        $guarantees = Guarantee::latest()->pluck('name' , 'id');
        $times = Time::latest()->pluck('name' , 'id');
        $posts = Post::where('id' , $post->id)->with('review','guarantee','category','time','brand')->first();
        return Inertia::render('Admin/Variety/EditVariety', [
            'categories' => $categories,
            'posts' => $posts,
            'times' => $times,
            'brands' => $brands,
            'guarantees' => $guarantees,
        ]);
    }
}
