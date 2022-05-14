<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function sendQuestion(Request $request){
        if(auth()->user()){
            Question::create([
                'post_id' => $request->post,
                'user_id' => auth()->user()->id,
                'body' => $request->body,
                'parent_id' => $request->parentId,
            ]);
            $post = Post::where('id', $request->post)->pluck('title')->first();
            Event::create([
                'type' => 13,
                'title' => 'پرسش و پاسخ',
                'user_id' => auth()->user()->id,
                'description' => 'کاربر با نام ' . auth()->user()->name . ' به محصول ' . $post . ' پرسش اضافه کرد',
            ]);
            return 'success';
        }else{
            return 'noUser';
        }
    }
    public function sendCall(Request $request){
        Notification::create([
            'title' => 'تماس با ما',
            'body' => $request->forms,
            'type' => 3,
        ]);
        return 'success';
    }
}
