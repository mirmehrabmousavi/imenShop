<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Event;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function sendComment(Request $request){
        $user = auth()->user();
        if (!$user){
            return 'noUser';
        }else{
            $limit = Setting::where('key' , 'limited')->pluck('value')->first();
            $forbiddens = Setting::where('key' , 'forbiddens')->pluck('value')->first();
            $commentLimited = Post::where('id' , $request->post['id'])->with(["comments" => function($q){
                $q->latest()->where('user_id' , auth()->user()->id);
            }])->first();
            $check = $commentLimited->comments->count();
            if ($check >= $limit){
                return 'limit';
            }
            else{
                $words = explode(' ' , $request->title);
                $words2 = explode(' ' , $request->body);
                $forbidden = explode(',' , $forbiddens);
                $array = array_intersect($words, $forbidden);
                $array2 = array_intersect($words2, $forbidden);

                if (count($array) == 0 && count($array2) ==0){
                    $commentSend = Comment::create([
                        'post_id' => $request->post['id'],
                        'user_id' => auth()->user()->id,
                        'title' => $request->title,
                        'body' => $request->body,
                        'rate' => $request->rate,
                        'name' => $request->UserName,
                        'email' => $request->emailUser,
                        'status' => $request->status,
                        'bad' => $request->bads,
                        'good' => $request->goods,
                    ]);
                    Event::create([
                        'type' => 4,
                        'title' => 'دیدگاه',
                        'user_id' => auth()->user()->id,
                        'description' => 'کاربر با نام ' . auth()->user()->name . '  برای محصول ' . $request->post['title'] . ' دیدگاهی با آیدی ' . $commentSend->id . ' ارسال کرد',
                    ]);
                    return 'success';
                }else{
                    return 'badWord' ;
                }
            }
        }
    }
    public function sendReply(Request $request){
        $user = auth()->user();
        if (!$user){
            return 'noUser';
        }else{
            $limit = Setting::where('key' , 'limited')->pluck('value')->first();
            $commentLimited = Post::where('id' , $request->post['id'])->with(["comments" => function($q){
                $q->latest()->where('user_id' , auth()->user()->id);
            }])->first();
            $check = $commentLimited->comments->count();
            if ($check >= $limit){
                return 'limit';
            }
            else{
                $comment = Comment::create([
                    'post_id' => $request->post['id'],
                    'user_id' => auth()->user()->id,
                    'body' => $request->reply,
                    'parent_id'=>$request->commentId,
                ]);
                Event::create([
                    'type' => 4,
                    'title' => 'دیدگاه',
                    'user_id' => auth()->user()->id,
                    'description' => 'کاربر با نام ' . auth()->user()->name . '  در محصول ' . $request->post['title'] . ' برای دیدگاهی با آیدی ' . $request->commentId . ' پاسخ ارسال کرد',
                ]);
                return   $commentPost= Comment::where('id' , $comment->id)->first();
            }
        }
    }
    public function getComment(Request $request){
        $post = Post::where('id' , $request->postID)->first();
        $approve = Setting::where('key' , 'approved')->pluck('value')->first();
        $pages = Setting::where('key' , 'pages')->pluck('value')->first();

        if ($approve == 0){
            return $comment = $post->comments()->latest()->with(["comments" => function($q){
                $q->latest();
            }])->where('parent_id' , 0)->paginate($pages);
        }else{
            return  $comment = $post->comments()->latest()->where('approved' , 1)->with(["comments" => function($q){
                $q->where('approved', '=', 1)->latest();
            }])->where('parent_id' , 0)->paginate($pages);

        }
    }
}
