<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index(Request $request){
        $adds = 0;
        $edits = 0;
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش رویداد')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف رویداد')->pluck('name');
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
                $tax = Event::where('id', $value)->first();
            }
            DB::table('events')->whereIn('id', $request->value)->delete();
        }
        if ($request->search){
            $search = Event::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
        }else{
            $search = Event::latest()->pluck('id')->toArray();
        }
        if ($request->date){
            $date = Event::whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = Event::latest()->pluck('id')->toArray();
        }
        if ($request->type){
            $type = Event::where('type' , $request->type)->pluck('id')->toArray();
        }else{
            $type = Event::latest()->pluck('id')->toArray();
        }
        $arrayFilter = array_intersect($search,$date,$type);
        $taxes = Event::latest()->whereIn('id' , $arrayFilter)->paginate(120);
        $labels = ['#','عنوان','توضیح','تاریخ ثبت','عملیات'];
        $name = 'رویداد';
        $url = 'event';
        Inertia::setRootView('admin');
        return Inertia::render('EventPanel' , [
            'taxes' => $taxes,
            'labels' => $labels,
            'deletes' => $deletes,
            'shows' => $shows,
            'adds' => $adds,
            'edits' => $edits,
            'name' => $name,
            'url' => $url,
        ]);
    }
}
