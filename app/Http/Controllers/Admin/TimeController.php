<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Time;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TimeController extends Controller
{
    public function index(Request $request){
        $showSome2 =  auth()->user()->getAllPermissions()->where('name' , 'نمایش زمان')->pluck('name');
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش زمان')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف زمان')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن زمان')->pluck('name');
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
        if(auth()->user()->admin == 1 or count($showSome2) >= 1){
            $shows = 1;
        }else{
            $shows = 0;
        }

        if($request->value){
            foreach ($request->value as $value) {
                $tax = Time::where('id', $value)->first();
                $tax->cart()->detach();
                $tax->pay()->detach();
            }
            DB::table('times')->whereIn('id', $request->value)->delete();
        }
        if($request->name){
            $request->validate([
                'day' => 'required',
                'from' => 'required',
                'to' => 'required',
            ]);
            if($request->taxId){
                $tax = Time::where('id' , $request->taxId)->first();
                $tax->update([
                    'name'=> $request->name,
                    'nameEn'=> $request->nameEn,
                    'day'=> $request->day,
                    'from'=> $request->from,
                    'to'=> $request->to,
                    'updated_at'=> Carbon::now(),
                ]);
            }else{
                $tax = Time::create([
                    'name'=> $request->name,
                    'nameEn'=> $request->nameEn,
                    'day'=> $request->day,
                    'from'=> $request->from,
                    'to'=> $request->to,
                    'user_id'=> auth()->user()->id,
                ]);
            }
        }
        if($request->taxId && !$request->name){
            $taxEdit = Time::where('id' , $request->taxId)->first();
        }else{
            $taxEdit = '';
        }


        if ($request->search){
            $search = Time::where("day" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            if(count($search) == 0){
                $search = Time::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            }
        }else{
            $search = Time::latest()->pluck('id')->toArray();
        }
        if ($request->date){
            $date = Time::whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = Time::latest()->pluck('id')->toArray();
        }

        $arrayFilter = array_intersect($search,$date);
        $taxes = Time::latest()->whereIn('id' , $arrayFilter)->paginate(30);
        $name='زمان';
        $routeAddress='time';
        $sidebar= '14';
        $labels = ['#','روز','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('AllTaxonami' , [
            'name' => $name,
            'taxes' => $taxes,
            'adds' => $adds,
            'labels' => $labels,
            'edits' => $edits,
            'deletes' => $deletes,
            'shows' => $shows,
            'taxEdit' => $taxEdit,
            'routeAddress' => $routeAddress,
            'sidebar' => $sidebar,
        ]);
    }
}
