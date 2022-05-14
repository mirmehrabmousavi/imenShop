<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request){
        $user = auth()->user();
        if(!$user){
            return 'noUser';
        }
        $like = Like::where('post_id' , $request->postID)->where('user_id' , $user->id)->first();
        $post = Post::where('id', $request->postID)->pluck('title')->first();
        if($like){
            $like->delete();
            Event::create([
                'type' => 5,
                'title' => 'علاقه مندی',
                'user_id' => auth()->user()->id,
                'description' => 'کاربر با نام ' . auth()->user()->name . ' محصول ' . $post . ' از نشانه هایش حذف کرد',
            ]);
            return 'delete';
        }else{
            $like = Like::create([
                'user_id'=>$user->id,
                'post_id'=>$request->postID,
            ]);
            Event::create([
                'type' => 5,
                'title' => 'علاقه مندی',
                'user_id' => auth()->user()->id,
                'description' => 'کاربر با نام ' . auth()->user()->name . ' محصول ' . $post . ' به علاقه مندی هایش اضافه کرد',
            ]);
            return $like;
        }
    }
    public function getLike(){
        if (auth()->user()){
            $user = auth()->user();
            return $like = Like::where('user_id' , $user->id)->pluck('post_id');
        }else{
            return [];
        }
    }
}
