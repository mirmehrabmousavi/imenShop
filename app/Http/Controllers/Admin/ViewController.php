<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\View;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ViewController extends Controller
{
    public function index(Request $request){
        $adds = 0;
        $edits = 0;
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش بازدید')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف بازدید')->pluck('name');
        if(auth()->user()->admin == 1 or count($checkDeletes) >= 1){
            $deletes = 1;
        }else{
            $deletes = 0;
        }
        if(auth()->user()->admin == 1 or count($showSome) >= 1){
            $shows = 1;
        }else{
            $shows = 0;
        }
        if($request->value){
            foreach ($request->value as $value) {
                $tax = View::where('id', $value)->first();
            }
            DB::table('views')->whereIn('id', $request->value)->delete();
        }
        if ($request->search){
            $search = View::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
        }else{
            $search = View::latest()->pluck('id')->toArray();
        }
        if ($request->date){
            $date = View::whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = View::latest()->pluck('id')->toArray();
        }
        $arrayFilter = array_intersect($search,$date);
        $name='بازدید';
        $routeAddress='view';
        $sidebar= '3';
        $taxes = View::latest()->whereIn('id' , $arrayFilter)->paginate(60);
        $labels = ['#','آی پی','سیستم','مرورگر','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('AllTaxonami' , [
            'taxes' => $taxes,
            'labels' => $labels,
            'deletes' => $deletes,
            'shows' => $shows,
            'name' => $name,
            'adds' => $adds,
            'edits' => $edits,
            'routeAddress' => $routeAddress,
            'sidebar' => $sidebar,
        ]);
    }
}
