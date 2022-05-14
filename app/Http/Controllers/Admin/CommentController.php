<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function index(Request $request){
        DB::table('comments')->where('seen', 0)->update(['seen' => 1]);
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش دیدگاه')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف دیدگاه')->pluck('name');
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
            DB::table('comments')->whereIn('id', $request->value)->delete();
        }
        if($request->sort == 0){
            $sort = Comment::latest()->pluck('id')->toArray();
        }
        if($request->sort == 1){
            $sort = Comment::latest()->where('approved' , 0)->pluck('id')->toArray();
        }
        if($request->sort == 2){
            $sort = Comment::latest()->where('approved' , 1)->pluck('id')->toArray();
        }
        if($request->date){
            $date = Comment::latest()->whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = Comment::latest()->pluck('id')->toArray();
        }
        if($request->search){
            $search = Comment::latest()->where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
        }else{
            $search = Comment::latest()->pluck('id')->toArray();
        }

        $arrayFilter = array_intersect($sort,$date,$search);

        if($request->page){
            $comments = Comment::latest()->whereIn('id' , $arrayFilter)->with('user' , 'post')->paginate($request->page);
        }else{
            $comments = Comment::latest()->whereIn('id' , $arrayFilter)->with('user' , 'post')->paginate(25);
        }
        Inertia::setRootView('admin');
        return Inertia::render('CommentPanel' , [
            'comments' => $comments,
            'deletes' => $deletes,
            'edits' => $edits,
        ]);
    }
    public function edit(Comment $comment , Request $request){
        if($request->body){
            $comment->update([
                'body' => $request->body,
                'title' => $request->title,
                'approved' => $request->approved,
                'bad' => $request->bads,
                'good' => $request->goods,
                'rate' => $request->rate,
                'status' => $request->status,
            ]);
            return redirect('/admin/comment');
        }
        Inertia::setRootView('admin');
        $comments = Comment::latest()->where('id' , $comment->id)->with('post')->first();
        return Inertia::render('EditComment' , [
            'comments' => $comments,
        ]);
    }
}
