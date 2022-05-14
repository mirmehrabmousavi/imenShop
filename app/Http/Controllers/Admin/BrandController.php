<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BrandController extends Controller
{
    public function index(Request $request){
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش برند های خودش')->pluck('name');
        $showSome2 =  auth()->user()->getAllPermissions()->where('name' , 'نمایش همه برند ها')->pluck('name');
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش برند')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف برند')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن برند')->pluck('name');
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
        if(auth()->user()->admin == 1 or count($showSome) >= 1 or count($showSome2) >= 1){
            $shows = 1;
        }else{
            $shows = 0;
        }

        if($request->value){
            foreach ($request->value as $value) {
                $tax = Brand::where('id', $value)->first();
                $tax->user()->detach();
                $tax->post()->detach();
            }
            DB::table('brands')->whereIn('id', $request->value)->delete();
        }
        if($request->name){
            $request->validate([
                'name' => 'required|max:220',
            ]);
            if($request->taxId){
                $tax = Brand::where('id' , $request->taxId)->first();
                $tax->update([
                    'name'=> $request->name,
                    'nameEn'=> $request->nameEn,
                    'image'=> $request->image,
                    'slug'=> $request->slug,
                    'updated_at'=> Carbon::now(),
                ]);
            }else{
                $tax = Brand::where('name' , $request->name)->first();
                if (!$tax){
                    $tax = Brand::create([
                        'name'=> $request->name,
                        'nameEn'=> $request->nameEn,
                        'slug'=> $request->slug,
                        'image'=> $request->image,
                    ]);
                    auth()->user()->brand()->sync($tax->id);
                }
            }
        }
        if($request->taxId && !$request->name){
            $taxEdit = Brand::where('id' , $request->taxId)->first();
        }else{
            $taxEdit = '';
        }


        if ($request->search){
            if (count($showSome) >= 1){
                $search = auth()->user()->brand()->where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = auth()->user()->brand()->where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }else{
                $search = Brand::where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = Brand::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }
        }else{
            if(count($showSome) >= 1){
                $search = auth()->user()->brand()->pluck('id')->toArray();
            }else{
                $search = Brand::latest()->pluck('id')->toArray();
            }
        }
        if ($request->date){
            if (count($showSome) >= 1){
                $date = auth()->user()->brand()->whereDate('created_at',$request->date)->pluck('id')->toArray();
            }else{
                $date = Brand::whereDate('created_at',$request->date)->pluck('id')->toArray();
            }
        }else{
            if(count($showSome) >= 1){
                $date = auth()->user()->brand()->pluck('id')->toArray();
            }else{
                $date = Brand::latest()->pluck('id')->toArray();
            }
        }

        $arrayFilter = array_intersect($search,$date);
        $taxes = Brand::latest()->whereIn('id' , $arrayFilter)->paginate(30);
        $name='برند';
        $routeAddress='brand';
        $sidebar= '4';
        $labels = ['#','آیدی','نام','پیوند','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('AllTaxonami' , [
            'name' => $name,
            'taxes' => $taxes,
            'adds' => $adds,
            'labels' => $labels,
            'sidebar' => $sidebar,
            'edits' => $edits,
            'deletes' => $deletes,
            'shows' => $shows,
            'taxEdit' => $taxEdit,
            'routeAddress' => $routeAddress,
        ]);
    }
}
