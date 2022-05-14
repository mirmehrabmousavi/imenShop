<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Guarantee;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Post;
use App\Models\Time;
use App\Models\User;
use App\Models\PayMeta;
use App\Models\Rate;
use App\Models\Review;
use App\Models\Tag;
use App\Traits\SendEmailTrait;
use App\Traits\SendSmsTrait;
use Illuminate\Support\Facades\Cache;
use Hekmatinasser\Verta\Verta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Melipayamak\MelipayamakApi;

class PostController extends Controller
{
    use SendSmsTrait;
    use SendEmailTrait;
    public function index(Request $request){
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش کالا های خودش')->pluck('name');
        $showSome2 =  auth()->user()->getAllPermissions()->where('name' , 'نمایش همه کالا ها')->pluck('name');
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش کالا')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف کالا')->pluck('name');
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
                    $post->post()->update(array(
                        'variety' => 0,
                    ));
                    Review::where('id' , $post->review()->pluck('id')->first())->delete();
                }
            }
            DB::table('posts')->whereIn('id', $request->value)->delete();
        }

        if($request->postShow){
            $showPosts = Post::where('id',$request->postShow)->where('variety',0)->with('user' , 'review')->withCount('comments','view')->first();
        }else{
            $showPosts = [];
        }

        if($request->postId){
            $request->validate([
                'title' => 'required|max:220',
                'image' => 'required',
            ]);
            $post = Post::where('id' , $request->postId)->where('variety',0)->first();
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
            $getPost = Post::where('id' , $request->postEdit)->where('variety',0)->with('review')->first();
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
                if($categoryName){
                    $category = $categoryName->post()->with('category')->pluck('id')->toArray();
                }
            }else{
                $categoryName = Category::latest()->where('name' , $request->category)->first();
                if($categoryName){
                    $category = $categoryName->post()->with('category')->pluck('id')->toArray();
                }
            }
        }else{
            if (count($showSome) >= 1){
                $category = auth()->user()->post()->with('category')->latest()->pluck('id')->toArray();
            }else{
                $category = Post::latest()->with('category')->withCount(["post" => function($q){
                    $q->where('status' ,1);
                }])->pluck('id')->toArray();
            }
        }

        $categories = Category::latest()->get();
        if ($request->sort == 1){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->where('status' , '0')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->where('status' , '0')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->where('status' , '0')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->where('status' , '0')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->where('status' , '0')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '0')->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '0')->paginate($page);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->where('status' , '0')->whereIn('id',$category)->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->where('status' , '0')->whereIn('id',$category)->paginate($page);
                    }
                }
            }
        }elseif($request->sort == 2){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->where('status' , '1')->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->where('status' , '1')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->where('status' , '1')->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->where('status' , '1')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->where('status' , '1')->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '1')->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->whereDate('created_at',$request->date)->whereIn('id',$category)->where('status' , '1')->paginate($page);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->where('status' , '1')->whereIn('id',$category)->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->where('status' , '1')->whereIn('id',$category)->paginate($page);
                    }
                }
            }
        }else{
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->whereIn('id',$category)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->whereIn('id',$category)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->whereIn('id',$category)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                    }
                    if (count($posts) == 0){
                        if (count($showSome) >= 1){
                            $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }else{
                            $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->whereIn('id',$category)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                        }
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->whereIn('id',$category)->whereDate('created_at',$request->date)->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->whereIn('id', $category)->whereDate('created_at',$request->date)->paginate($page);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $posts = auth()->user()->post()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->latest()->whereIn('id', $category)->paginate($page);
                    }else{
                        $posts = Post::latest()->with('category')->where('type',0)->withCount(["post" => function($q){
                            $q->where('status' ,1);
                        }])->where('variety',0)->whereIn('id',$category)->paginate($page);
                    }
                }
            }
        }
        $labels = ['آیدی','تصویر','عنوان','وضعیت','مبلغ','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('AllPost' , [
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
                'count' => 'required|integer|digits_between: 1,5',
                'price' => 'required|integer|digits_between: 1,9',
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
                'showcase' => $request->showcase,
                'used' => $request->used,
                'original' => $request->original,
                'status' => $request->status,
                'slug' => $request->slug,
                'image' => $request->image,
                'score' => $request->score,
                'price' => $price,
                'type' => 0,
                'offPrice' => $request->price,
                'off' => $request->off,
                'suggest' => $request->suggest,
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
            $post->time()->sync($request->allTime);
        }
        if($request->title == '' && $request->copy){
            $copy = Post::where('id' , $request->copy)->with('review','guarantee','category','time','brand')->first();
        }else{
            $copy = '';
        }
        $categories = Category::latest()->pluck('name', 'id');
        $brands = Brand::latest()->pluck('name', 'id');
        $guarantees = Guarantee::latest()->pluck('name', 'id');
        $times = Time::latest()->pluck('name', 'id');
        Inertia::setRootView('admin');
        return Inertia::render('PostCreate', [
            'categories' => $categories,
            'times' => $times,
            'brands' => $brands,
            'copy' => $copy,
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
        $posts = Post::where('id',$post->id)->with('review' , 'user')->withCount('bookmark' , 'review' , 'like' , 'view' , 'comments')->first();
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
        if($request->title or $request->image or $request->body or $request->summery){
            $request->validate([
                'title' => 'required|max:220',
                'image' => 'required',
                'status' => 'required',
                'count' => 'required|integer|digits_between: 1,5',
                'price' => 'required|integer|digits_between: 1,9',
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
                'showcase' => $request->showcase,
                'used' => $request->used,
                'score' => $request->score,
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
                'size' => $request->allSize,
                'specifications' => $request->allProperty,
                'colors' => $request->allColor,
            ]);
            $post->category()->detach();
            $post->brand()->detach();
            $post->guarantee()->detach();
            $post->time()->detach();
            $post->category()->sync($request->allCategory);
            $post->brand()->sync($request->allBrand);
            $post->guarantee()->sync($request->allGuarantee);
            $post->time()->sync($request->allTime);
            if($post->suggest){
                $reports = Report::where('reportable_id' , $post->id)->where('type',2)->get();
                $address1 = Setting::where('key' , 'address')->pluck('value')->first();
                $address = $address1 . 'product/' .$post->slug;
                foreach ($reports as $item){
                    $user = User::where('id' , $item->user_id)->first();
                    foreach (json_decode($item->data , true) as $value){
                        if($value == 'ایمیل'){
                            if($user->email){
                                $message = "<strong>سلام و درود خدمت شما دوست عزیز</strong><br>محصول $post->title <br> در پیشنهاد شگفت انگیز قرار گرفته است <br><a href='$address '>مشاهده محصول</a><br><br>";
                                $this->sendEmail($user->email , $message , 'شگفت انگیز ها');
                            }
                        }
                        if($value == 'پیامک'){
                            if($user->number){
                                $message = "سلام و درود خدمت شما دوست عزیز\n محصول  $post->title\nدر پیشنهاد شگفت انگیز قرار گرفته است";
                                $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                                if($sms == 1){
                                    $username = '';
                                    $password = '';
                                    $api = new MelipayamakApi($username,$password);
                                    $sms = $api->sms();
                                    $to = $user->number;
                                    $from = '';
                                    $text = $message;
                                    $response = $sms->send($to,$from,$text);
                                }else{
                                    $this->sendSms("$user->number" , $message,env('GHASEDAKAPI_Number'));
                                }
                            }
                        }

                    }
                }
            }
        }
        $categories = Category::latest()->pluck('name' , 'id');
        $brands = Brand::latest()->pluck('name' , 'id');
        $guarantees = Guarantee::latest()->pluck('name' , 'id');
        $times = Time::latest()->pluck('name' , 'id');
        $posts = Post::where('id' , $post->id)->with('review','guarantee','category','time','brand')->first();
        Inertia::setRootView('admin');
        return Inertia::render('EditPost', [
            'categories' => $categories,
            'posts' => $posts,
            'times' => $times,
            'brands' => $brands,
            'guarantees' => $guarantees,
        ]);
    }
}
