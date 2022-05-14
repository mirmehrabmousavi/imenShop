<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guarantee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GuaranteeController extends Controller
{
    public function index(Request $request){
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش گارانتی ها')->pluck('name');
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش گارانتی')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف گارانتی')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن گارانتی')->pluck('name');
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
        if(auth()->user()->admin == 1 or count($showSome) >= 1){
            $shows = 1;
        }else{
            $shows = 0;
        }

        if($request->value){
            foreach ($request->value as $value) {
                $tax = Guarantee::where('id', $value)->first();
                $tax->user()->detach();
                $tax->post()->detach();
            }
            DB::table('guarantees')->whereIn('id', $request->value)->delete();
        }
        if($request->name){
            $request->validate([
                'name' => 'required|max:220',
            ]);
            if($request->taxId){
                $tax = Guarantee::where('id' , $request->taxId)->first();
                $tax->update([
                    'name'=> $request->name,
                    'nameEn'=> $request->nameEn,
                    'slug'=> $request->slug,
                    'updated_at'=> Carbon::now(),
                ]);
            }else{
                $tax = Guarantee::where('name' , $request->name)->first();
                if (!$tax){
                    $tax = Guarantee::create([
                        'name'=> $request->name,
                        'nameEn'=> $request->nameEn,
                        'slug'=> $request->slug,
                    ]);
                    auth()->user()->guarantee()->sync($tax->id);
                }
            }
        }
        if($request->taxId && !$request->name){
            $taxEdit = Guarantee::where('id' , $request->taxId)->first();
        }else{
            $taxEdit = '';
        }


        if ($request->search){
            if (count($showSome) >= 1){
                $search = auth()->user()->guarantee()->where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = auth()->user()->guarantee()->where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }else{
                $search = Guarantee::where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = Guarantee::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }
        }else{
            if(count($showSome) >= 1){
                $search = auth()->user()->guarantee()->pluck('id')->toArray();
            }else{
                $search = Guarantee::latest()->pluck('id')->toArray();
            }
        }
        if ($request->date){
            if (count($showSome) >= 1){
                $date = auth()->user()->guarantee()->whereDate('created_at',$request->date)->pluck('id')->toArray();
            }else{
                $date = Guarantee::whereDate('created_at',$request->date)->pluck('id')->toArray();
            }
        }else{
            if(count($showSome) >= 1){
                $date = auth()->user()->guarantee()->pluck('id')->toArray();
            }else{
                $date = Guarantee::latest()->pluck('id')->toArray();
            }
        }

        $arrayFilter = array_intersect($search,$date);
        $taxes = Guarantee::latest()->whereIn('id' , $arrayFilter)->paginate(30);
        $taxesSend = Guarantee::latest()->pluck('name','id');
        $name='گارانتی';
        $routeAddress='guarantee';
        $sidebar= '4';
        $labels = ['#','آیدی','نام','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('AllTaxonami' , [
            'name' => $name,
            'taxes' => $taxes,
            'taxesSend' => $taxesSend,
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
