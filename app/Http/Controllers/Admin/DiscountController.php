<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DiscountController extends Controller
{
    public function index(Request $request){
        if($request->value){
            foreach ($request->value as $value) {
                $tax = Discount::where('id', $value)->first();
            }
            DB::table('discounts')->whereIn('id', $request->value)->delete();
        }
        if($request->name){
            $request->validate([
                'name' => 'required|max:220',
                'percent' => 'required|max:3',
                'code' => 'required|max:20',
                'count' => 'required|max:20',
            ]);
            if($request->taxId){
                $tax = Discount::where('id' , $request->taxId)->first();
                $tax->update([
                    'title'=> $request->name,
                    'code'=> $request->code,
                    'day'=> $request->day,
                    'percent'=> $request->percent,
                    'status'=> $request->status,
                    'count'=> $request->count,
                    'user_id'=> auth()->user()->id,
                    'post_id'=> $request->postId,
                    'updated_at'=> Carbon::now(),
                ]);
            }else{
                $tax = Discount::where('title' , $request->name)->first();
                if (!$tax){
                    $tax = Discount::create([
                        'title'=> $request->name,
                        'code'=> $request->code,
                        'day'=> $request->day,
                        'percent'=> $request->percent,
                        'status'=> $request->status,
                        'count'=> $request->count,
                        'user_id'=> auth()->user()->id,
                        'post_id'=> $request->postId,
                    ]);
                }
            }
        }
        if($request->taxId && !$request->name){
            $taxEdit = Discount::where('id' , $request->taxId)->with('post')->first();
        }else{
            $taxEdit = '';
        }


        if ($request->search){
            $search = Discount::where("title" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            if(count($search) == 0){
                $search = Discount::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            }
        }else{
            $search = Discount::latest()->pluck('id')->toArray();
        }
        if ($request->date){
            $date = Discount::whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = Discount::latest()->pluck('id')->toArray();
        }

        $arrayFilter = array_intersect($search,$date);
        $taxes = Discount::latest()->whereIn('id' , $arrayFilter)->paginate(30);
        $name='کد تخفیف';
        $routeAddress='discount';
        $sidebar= '4';
        $labels = ['#','آیدی','عنوان','وضعیت','تاریخ ثبت','عملیات'];
        $adds = 1;
        $edits = 1;
        $deletes = 1;
        $shows = 0;
        $posts = Post::latest()->select(['id','title'])->take(150)->get();
        Inertia::setRootView('admin');
        return Inertia::render('AllTaxonami' , [
            'name' => $name,
            'taxes' => $taxes,
            'posts' => $posts,
            'labels' => $labels,
            'adds' => $adds,
            'edits' => $edits,
            'deletes' => $deletes,
            'shows' => $shows,
            'taxEdit' => $taxEdit,
            'routeAddress' => $routeAddress,
            'sidebar' => $sidebar,
        ]);
    }
}
