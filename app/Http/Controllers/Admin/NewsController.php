<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index(Request $request){
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش پست های خودش')->pluck('name');
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

        if($request->value){
            foreach ($request->value as $value) {
                $post = News::where('id', $value)->first();
                $post->category()->detach();
                $post->tag()->detach();
            }
            DB::table('news')->whereIn('id', $request->value)->delete();
        }

        if ($request->getPage){
            $page = $request->getPage;
        }else{
            $page = '25';
        }

        if ($request->category){
            if (count($showSome) >= 1){
                $categoryName = auth()->user()->category()->latest()->where('name' , $request->category)->first();
                $category = $categoryName->news()->pluck('id')->toArray();
            }else{
                $categoryName = Category::latest()->where('name' , $request->category)->first();
                $category = $categoryName->news()->pluck('id')->toArray();
            }
        }else{
            if (count($showSome) >= 1){
                $category = auth()->user()->news()->latest()->pluck('id')->toArray();
            }else{
                $category = News::latest()->pluck('id')->toArray();
            }
        }
        $categories = Category::latest()->get();
        if ($request->sort == 1){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->latest()->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = News::latest()->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->news()->latest()->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = News::latest()->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->news()->latest()->where('status' , '0')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = News::latest()->where('status' , '0')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->news()->latest()->where('status' , '0')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = News::latest()->where('status' , '0')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->news()->latest()->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '0')->paginate($page);
                    }else{
                        $posts = News::latest()->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '0')->paginate($page);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->news()->latest()->where('status' , '0')->whereIn('id',$category)->paginate($page);
                    }else{
                        $posts = News::latest()->where('status' , '0')->whereIn('id',$category)->paginate($page);
                    }
                }
            }
        }elseif($request->sort == 2){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->news()->latest()->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = News::latest()->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->news()->latest()->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = News::latest()->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->news()->latest()->where('status' , '1')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = News::latest()->where('status' , '1')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->news()->latest()->where('status' , '1')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = News::latest()->where('status' , '1')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->news()->latest()->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '1')->paginate($page);
                    }else{
                        $posts = News::latest()->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '1')->paginate($page);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->news()->latest()->where('status' , '1')->whereIn('id',$category)->paginate($page);
                    }else{
                        $posts = News::latest()->where('status' , '1')->whereIn('id',$category)->paginate($page);
                    }
                }
            }
        }else{
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->news()->latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = News::latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->news()->latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = News::latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->news()->latest()->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = News::latest()->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->news()->latest()->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = News::latest()->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->news()->latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->paginate($page);
                    }else{
                        $posts = News::latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->paginate($page);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->news()->latest()->whereIn('id',$category)->paginate($page);
                    }else{
                        $posts = News::latest()->whereIn('id',$category)->paginate($page);
                    }
                }
            }
        }
        $labels = ['آیدی','تصویر','عنوان','وضعیت','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('AllNews' , [
            'posts' => $posts,
            'labels' => $labels,
            'edits' => $edits,
            'deletes' => $deletes,
            'categories' => $categories,
        ]);
    }
    public function edit(Request $request, News $news){
        if($request->title or $request->image or $request->body or $request->summery or $request->day or $request->name or $request->background){
            $request->validate([
                'title' => 'required|max:220',
                'image' => 'required',
                'status' => 'required',
                'body' => 'required',
                'time' => 'required|integer|digits_between: 1,5',
            ]);
            $news->update([
                'body'=>$request->body,
                'title'=>$request->title,
                'bodyEn'=>$request->bodyEn,
                'titleEn'=>$request->titleEn,
                'status'=>$request->status,
                'image'=>$request->image,
                'suggest'=>$request->suggest,
                'accept'=>$request->accept,
                'time'=>$request->time,
                'slug'=>$request->slug,
                'updated_at'=> Carbon::now(),
            ]);
            $news->category()->detach();
            $news->tag()->detach();
            $news->category()->sync($request->allCategory);
            $news->tag()->sync($request->allTag);
        }
        $news = News::where('id' , $news->id)->with('tag','category')->first();
        $categories = Category::latest()->pluck('name' , 'id');
        $tags = Tag::latest()->pluck('name' , 'id');
        Inertia::setRootView('admin');
        return Inertia::render('EditNews' , [
            'news' => $news,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
    public function create(Request $request){
        if($request->title or $request->image or $request->body){
            $request->validate([
                'title' => 'required|max:220',
                'image' => 'required',
                'status' => 'required',
                'body' => 'required',
                'time' => 'required|integer|digits_between: 1,5',
            ]);
            $post = News::create([
                'body'=>$request->body,
                'title'=>$request->title,
                'status'=>$request->status,
                'image'=>$request->image,
                'suggest'=>$request->suggest,
                'accept'=>$request->accept,
                'bodyEn'=>$request->bodyEn,
                'titleEn'=>$request->titleEn,
                'time'=>$request->time,
                'slug'=>$request->slug,
                'user_id'=>auth()->user()->id,
            ]);
            $post->category()->sync($request->allCategory);
            $post->tag()->sync($request->allTag);
        }
        $categories = Category::latest()->pluck('name' , 'id');
        $tags = Tag::latest()->pluck('name' , 'id');
        Inertia::setRootView('admin');
        return Inertia::render('CreateNews' , [
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
}
