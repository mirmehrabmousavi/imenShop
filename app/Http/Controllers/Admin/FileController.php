<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Guarantee;
use App\Models\Post;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Time;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FileController extends Controller
{
    public function index(Request $request){
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش پست های خودش')->pluck('name');
        $showSome2 =  auth()->user()->getAllPermissions()->where('name' , 'نمایش همه پست ها')->pluck('name');
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش پست')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف پست')->pluck('name');
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
        if(auth()->user()->admin == 1 or count($showSome) >= 1 or count($showSome2) >= 1){
            $shows = 1;
        }else{
            $shows = 0;
        }

        if($request->value){
            foreach ($request->value as $value) {
                $post = Post::where('id', $value)->first();
                if($post){
                    if($post->category()->count() >= 1)$post->category()->detach();
                    $post->bookmark()->delete();
                    $post->comments()->delete();
                    $post->like()->delete();
                    $post->payMeta()->delete();
                    $post->cart()->delete();
                    $post->rate()->delete();
                    $post->report()->delete();
                    $post->question()->delete();
                    $post->review()->detach();
                    $post->guarantee()->detach();
                    Review::where('id' , $post->review()->pluck('id')->first())->delete();
                }
            }
            DB::table('posts')->whereIn('id', $request->value)->delete();
        }

        if($request->postShow){
            $showPosts = Post::where('id',$request->postShow)->with('user' , 'review')->withCount('comments','view')->first();
        }else{
            $showPosts = [];
        }

        if($request->postId){
            $request->validate([
                'title' => 'required|max:220',
                'image' => 'required',
            ]);
            $post = Post::where('id' , $request->postId)->first();
            if ($request->off){
                $price = round($request->price - $request->price * $request->off / 100);
            }else{
                $price = $request->price;
            }
            $post->update([
                'summery'=>$request->summery,
                'title'=>$request->title,
                'count'=>$request->count,
                'image'=>$request->image,
                'status'=>$request->status,
                'slug'=>$request->slug,
                'price'=>$price,
                'offPrice'=>$request->price,
                'off'=>$request->off,
                'suggest'=>$request->suggest,
            ]);
            $post->review()->first()->update([
                'body'=>$request->body,
                'titleEn'=>$request->titleEn,
            ]);
        }

        if($request->postEdit){
            $getPost = Post::where('id' , $request->postEdit)->with('review')->first();
        }else{
            $getPost = '';
        }
        if ($request->getPage){
            $page = $request->getPage;
        }else{
            $page = '25';
        }

        if ($request->category){
            if (count($showSome) >= 1){
                $categoryName = auth()->user()->category()->latest()->where('name' , $request->category)->first();
                $category = $categoryName->post()->with('category')->where('type' , 1)->pluck('id')->toArray();
            }else{
                $categoryName = Category::latest()->where('name' , $request->category)->first();
                $category = $categoryName->post()->with('category')->where('type' , 1)->pluck('id')->toArray();
            }
        }else{
            if (count($showSome) >= 1){
                $category = auth()->user()->post()->with('category')->where('type' , 1)->latest()->pluck('id')->toArray();
            }else{
                $category = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                    $q->where('status' ,1);
                }])->pluck('id')->toArray();
            }
        }

        $categories = Category::latest()->get();
        if ($request->sort == 1){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->latest()->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                                $q->where('status' ,1);
                            }])->latest()->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                                $q->where('status' ,1);
                            }])->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->latest()->where('status' , '0')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('status' , '0')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                                $q->where('status' ,1);
                            }])->latest()->where('status' , '0')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                                $q->where('status' ,1);
                            }])->where('status' , '0')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->latest()->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '0')->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '0')->paginate($page);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->latest()->where('status' , '0')->whereIn('id',$category)->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('status' , '0')->whereIn('id',$category)->paginate($page);
                    }
                }
            }
        }elseif($request->sort == 2){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->latest()->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                                $q->where('status' ,1);
                            }])->latest()->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                                $q->where('status' ,1);
                            }])->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->latest()->where('status' , '1')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('status' , '1')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                                $q->where('status' ,1);
                            }])->latest()->where('status' , '1')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                                $q->where('status' ,1);
                            }])->where('status' , '1')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->latest()->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '1')->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '1')->paginate($page);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->latest()->where('status' , '1')->whereIn('id',$category)->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('status' , '1')->whereIn('id',$category)->paginate($page);
                    }
                }
            }
        }else{
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                                $q->where('status' ,1);
                            }])->latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                                $q->where('status' ,1);
                            }])->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->latest()->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                                $q->where('status' ,1);
                            }])->latest()->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                                $q->where('status' ,1);
                            }])->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->whereIn('id', $category)->whereDate('created_at',$request->date)->paginate($page);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->latest()->whereIn('id', $category)->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type' , 1)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->whereIn('id',$category)->paginate($page);
                    }
                }
            }
        }
        $labels = ['آیدی','تصویر','عنوان','وضعیت','مبلغ','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('AllFile' , [
            'labels' => $labels,
            'posts' => $posts,
            'getPost' => $getPost,
            'showPosts' => $showPosts,
            'edits' => $edits,
            'deletes' => $deletes,
            'shows' => $shows,
            'categories' => $categories,
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
                'count' => 10000,
                'title' => $request->title,
                'titleEn' => $request->titleEn,
                'showcase' => $request->showcase,
                'used' => 0,
                'original' => 0,
                'status' => $request->status,
                'slug' => $request->slug,
                'file' => $request->file,
                'image' => $request->image,
                'price' => $price,
                'offPrice' => $request->price,
                'off' => $request->off,
                'suggest' => $request->suggest,
                'user_id' => auth()->user()->id,
                'type' => 1,
                'product_id' => $productId . '-' . $productIds,
            ]);
            $meta = Review::create([
                'body' => $request->body,
                'bodyEn' => $request->bodyEn,
                'rate' => $request->allRate,
                'ability' => $request->allAbility,
                'specifications' => $request->allProperty,
            ]);
            $post->review()->sync($meta->id);
            $post->category()->sync($request->allCategory);
        }
        $categories = Category::latest()->pluck('name', 'id');
        Inertia::setRootView('admin');
        return Inertia::render('FileCreate', [
            'categories' => $categories,
        ]);
    }

    public function edit(Post $post , Request $request){
        if($request->title or $request->image or $request->body or $request->summery){
            $request->validate([
                'title' => 'required',
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
                'title' => $request->title,
                'titleEn' => $request->titleEn,
                'showcase' => $request->showcase,
                'used' => $request->used,
                'original' => $request->original,
                'status' => $request->status,
                'slug' => $request->slug,
                'image' => $request->image,
                'price' => $price,
                'offPrice' => $request->price,
                'off' => $request->off,
                'suggest' => $request->suggest,
                'updated_at'=> Carbon::now(),
            ]);
            $post->review()->first()->update([
                'body' => $request->body,
                'bodyEn' => $request->bodyEn,
                'rate' => $request->allRate,
                'ability' => $request->allAbility,
                'specifications' => $request->allProperty,
            ]);
            $post->category()->detach();
            $post->category()->sync($request->allCategory);
        }
        $categories = Category::latest()->pluck('name' , 'id');
        $posts = Post::where('id' , $post->id)->with('review','category')->first();
        Inertia::setRootView('admin');
        return Inertia::render('EditFile', [
            'categories' => $categories,
            'posts' => $posts,
        ]);
    }
}
