<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function feedback(Request $request){
        $adds = 0;
        $edits = 0;
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش بازخورد')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف بازخورد')->pluck('name');
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
                $tax = Report::where('id', $value)->first();
            }
            DB::table('reports')->whereIn('id', $request->value)->delete();
        }
        if ($request->search){
            $search = Report::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
        }else{
            $search = Report::latest()->pluck('id')->toArray();
        }
        if ($request->date){
            $date = Report::whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = Report::latest()->pluck('id')->toArray();
        }
        $arrayFilter = array_intersect($search,$date);
        $taxes = Report::latest()->whereIn('id' , $arrayFilter)->where('type' , 1)->with('user')->paginate(120);
        $labels = ['#','کاربر','بازخورد','توضیحات','تاریخ ثبت','عملیات'];
        $name = 'بازخورد';
        $url = 'report/feedback';
        return Inertia::render('Admin/Event/EventPanel' , [
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
    public function notification(Request $request){
        $adds = 0;
        $edits = 0;
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش اطلاع پست')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف اطلاع پست')->pluck('name');
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
                $tax = Report::where('id', $value)->first();
            }
            DB::table('reports')->whereIn('id', $request->value)->delete();
        }
        if ($request->search){
            $search = Report::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
        }else{
            $search = Report::latest()->pluck('id')->toArray();
        }
        if ($request->date){
            $date = Report::whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = Report::latest()->pluck('id')->toArray();
        }
        $arrayFilter = array_intersect($search,$date);
        $taxes = Report::latest()->whereIn('id' , $arrayFilter)->where('type' , 2)->with('user')->paginate(120);
        $labels = ['#','کاربر','بازخورد','تاریخ ثبت','عملیات'];
        $name = 'اطلاع رسانی از شگفتانه';
        $url = 'report/notification';
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
