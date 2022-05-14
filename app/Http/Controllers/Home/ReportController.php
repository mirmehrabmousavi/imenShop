<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function sendReport(Request $request){
        if(auth()->user()){
            Report::create([
                'user_id' => Auth::id(),
                'data' => $request->datas,
                'body' => $request->body,
                'type' => $request->type,
                'reportable_id' => $request->post_id,
                'reportable_type'=> 'App\\Models\\Post',
            ]);
            $post = Post::where('id', $request->post_id)->pluck('title')->first();
            Event::create([
                'type' => 7,
                'title' => 'بازخورد',
                'user_id' => auth()->user()->id,
                'description' => 'کاربر با نام ' . auth()->user()->name . ' به محصول ' . $post . ' بازخوردی اضافه کرد',
            ]);
            return 'success';
        }else{
            return 'noUser';
        }
    }
}
