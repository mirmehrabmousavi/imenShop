<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Event;
use App\Models\Post;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function store(Request $request){
        $user = auth()->user();
        if(!$user){
            return 'noUser';
        }
        $bookmark = Bookmark::where('post_id' , $request->postID)->where('user_id' , $user->id)->first();
        $post = Post::where('id', $request->postID)->pluck('title')->first();
        if($bookmark){
            $bookmark->delete();
            Event::create([
                'type' => 2,
                'title' => 'نشانه گذاری',
                'user_id' => auth()->user()->id,
                'description' => 'کاربر با نام ' . auth()->user()->name . ' محصول ' . $post . ' از نشانه هایش حذف کرد',
            ]);
            return 'delete';
        }else{
            $bookmark = Bookmark::create([
                'user_id'=>$user->id,
                'post_id'=>$request->postID,
            ]);
            Event::create([
                'type' => 2,
                'title' => 'نشانه گذاری',
                'user_id' => auth()->user()->id,
                'description' => 'کاربر با نام ' . auth()->user()->name . ' محصول ' . $post . ' به نشانه هایش اضافه کرد',
            ]);
            return $bookmark;
        }
    }
    public function getBookmark(){
        if (auth()->user()){
            $user = auth()->user();
            return $like = Bookmark::where('user_id' , $user->id)->pluck('post_id');
        }else{
            return [];
        }
    }
}
