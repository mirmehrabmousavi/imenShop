<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Rank;
use App\Models\Score;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ScoreController extends Controller
{
    public function index(Request $request) {
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش امتیاز')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف امتیاز')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن امتیاز')->pluck('name');
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
        if(auth()->user()->admin == 1 or count($checkAdds) >= 1){
            $adds = 1;
        }else{
            $adds = 0;
        }
        if($request->value){
            DB::table('scores')->whereIn('id', $request->value)->delete();
        }
        if($request->price){
            $request->validate([
                'price' => 'required|max:255',
            ]);
            if($request->scoreId){
                $score = Score::where('id' , $request->scoreId)->first();
                $score->update([
                    'name'=> $request->price,
                    'type'=> $request->type,
                    'user_id'=> $request->user,
                    'updated_at'=> Carbon::now(),
                ]);
            }else{
                Score::create([
                    'name'=> $request->price,
                    'type'=> $request->type,
                    'user_id'=> $request->user,
                ]);
            }
        }
        if($request->scoreId && !$request->price){
            $scoreEdit = Score::where('id' , $request->scoreId)->with('user')->first();
        }else{
            $scoreEdit = '';
        }
        if ($request->search){
            $user = User::where("name" , "LIKE" , "%{$request->search}%")->first();
            $search = $user->score()->pluck('id')->toArray();
            if(count($search) == 0){
                $search = Score::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            }
        }else{
            $search = Score::latest()->pluck('id')->toArray();
        }
        if ($request->date){
            $date = Score::whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = Score::latest()->pluck('id')->toArray();
        }
        $arrayFilter = array_intersect($search,$date);
        $scores = Score::latest()->whereIn('id' , $arrayFilter)->with('user')->paginate(30);
        $users = User::latest()->take(200)->get();
        $labels = ['#','امتیاز','نوع امتیاز','گیرنده','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('ScorePanel' , [
            'users' => $users,
            'adds' => $adds,
            'edits' => $edits,
            'labels' => $labels,
            'deletes' => $deletes,
            'scoreEdit' => $scoreEdit,
            'scores' => $scores,
        ]);
    }

    public function rank(Request $request) {
        if($request->value){
            foreach ($request->value as $value) {
                $tax = Rank::where('id', $value)->first();
                if($tax) $tax->post()->detach();
            }
            DB::table('ranks')->whereIn('id', $request->value)->delete();
        }
        if($request->rankId && !$request->price){
            $rankEdit = Rank::where('id' , $request->rankId)->first();
            $categories = $rankEdit->post;
        }else{
            $rankEdit = '';
            $categories = [];
        }
        if($request->update){
            $request->validate([
                'name' => 'required|max:255',
                'from' => 'required|max:255',
                'to' => 'required|max:255',
            ]);
            if($request->rankId){
                $rank = Rank::where('id' , $request->rankId)->first();
                $rank->update([
                    'name'=> $request->name,
                    'to'=> $request->to,
                    'from'=> $request->from,
                    'off'=> $request->off,
                    'updated_at'=> Carbon::now(),
                ]);
                $rank->post()->detach();
                $rank->post()->sync($request->postsId);
            }else{
                $rank = Rank::create([
                    'name'=> $request->name,
                    'to'=> $request->to,
                    'from'=> $request->from,
                    'off'=> $request->off,
                ]);
                $rank->post()->sync($request->postsId);
            }
        }
        if ($request->search){
            $search = Rank::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
        }else{
            $search = Rank::latest()->pluck('id')->toArray();
        }
        if ($request->date){
            $date = Rank::whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = Rank::latest()->pluck('id')->toArray();
        }
        $arrayFilter = array_intersect($search,$date);
        $ranks = Rank::latest()->whereIn('id' , $arrayFilter)->paginate(30);
        $posts = Post::latest()->select(['id','title'])->where('type',0)->where('variety',0)->take(100)->get();
        $labels = ['#','نام','از امتیاز','تا امتیاز','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('RankPanel' , [
            'labels' => $labels,
            'rankEdit' => $rankEdit,
            'ranks' => $ranks,
            'categories' => $categories,
            'posts' => $posts,
        ]);
    }
}
