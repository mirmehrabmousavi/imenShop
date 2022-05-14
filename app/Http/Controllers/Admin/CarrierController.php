<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CarrierController extends Controller
{
    public function index(Request $request){
        $showSome2 =  auth()->user()->getAllPermissions()->where('name' , 'نمایش حامل')->pluck('name');
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش حامل')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف حامل')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن حامل')->pluck('name');
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
                $tax = Carrier::where('id', $value)->first();
                $tax->user()->detach();
                $tax->post()->detach();
            }
            DB::table('carriers')->whereIn('id', $request->value)->delete();
        }
        if($request->name){
            $request->validate([
                'name' => 'required|max:220',
            ]);
            if($request->taxId){
                $tax = Carrier::where('id' , $request->taxId)->first();
                $tax->update([
                    'name'=> $request->name,
                    'price'=> $request->price,
                    'body'=> $request->body,
                    'limit'=> $request->limit,
                    'nameEn'=> $request->nameEn,
                    'updated_at'=> Carbon::now(),
                ]);
            }else{
                $tax = Carrier::where('name' , $request->name)->first();
                if (!$tax){
                    $tax = Carrier::create([
                        'price'=> $request->price,
                        'limit'=> $request->limit,
                        'name'=> $request->name,
                        'nameEn'=> $request->nameEn,
                        'body'=> $request->body,
                    ]);
                    auth()->user()->carrier()->sync($tax->id);
                }
            }
        }
        if($request->taxId && !$request->name){
            $taxEdit = Carrier::where('id' , $request->taxId)->first();
        }else{
            $taxEdit = '';
        }


        if ($request->search){
            if (count($showSome2) >= 1){
                $search = auth()->user()->carrier()->where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = auth()->user()->carrier()->where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }else{
                $search = Carrier::where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = Carrier::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }
        }else{
            if(count($showSome2) >= 1){
                $search = auth()->user()->carrier()->pluck('id')->toArray();
            }else{
                $search = Carrier::latest()->pluck('id')->toArray();
            }
        }
        if ($request->date){
            if (count($showSome2) >= 1){
                $date = auth()->user()->carrier()->whereDate('created_at',$request->date)->pluck('id')->toArray();
            }else{
                $date = Carrier::whereDate('created_at',$request->date)->pluck('id')->toArray();
            }
        }else{
            if(count($showSome2) >= 1){
                $date = auth()->user()->carrier()->pluck('id')->toArray();
            }else{
                $date = Carrier::latest()->pluck('id')->toArray();
            }
        }

        $arrayFilter = array_intersect($search,$date);
        $taxes = Carrier::latest()->whereIn('id' , $arrayFilter)->paginate(30);
        $name='حامل';
        $routeAddress='carrier';
        $sidebar= '14';
        $labels = ['#','مبلغ','نام','تاریخ ثبت','عملیات'];
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
