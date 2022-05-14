<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TagController extends Controller
{
    public function index(Request $request){
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش برچسب های خودش')->pluck('name');
        $showSome2 =  auth()->user()->getAllPermissions()->where('name' , 'نمایش همه برچسب ها')->pluck('name');
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش برچسب')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف برچسب')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن برچسب')->pluck('name');
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
                $tax = Tag::where('id', $value)->first();
                $tax->user()->detach();
                $tax->post()->detach();
            }
            DB::table('tags')->whereIn('id', $request->value)->delete();
        }
        if($request->name){
            $request->validate([
                'name' => 'required|max:220',
            ]);
            if($request->taxId){
                $tax = Tag::where('id' , $request->taxId)->first();
                $tax->update([
                    'name'=> $request->name,
                    'nameEn'=> $request->nameEn,
                    'image'=> $request->image,
                    'slug'=> $request->slug,
                    'updated_at'=> Carbon::now(),
                ]);
            }else{
                $tax = Tag::where('name' , $request->name)->first();
                if (!$tax){
                    $tax = Tag::create([
                        'name'=> $request->name,
                        'nameEn'=> $request->nameEn,
                        'image'=> $request->image,
                        'slug'=> $request->slug,
                    ]);
                    auth()->user()->tag()->sync($tax->id);
                }
            }
        }
        if($request->taxId && !$request->name){
            $taxEdit = Tag::where('id' , $request->taxId)->first();
        }else{
            $taxEdit = '';
        }


        if ($request->search){
            if (count($showSome) >= 1){
                $search = auth()->user()->tag()->where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = auth()->user()->tag()->where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }else{
                $search = Tag::where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = Tag::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }
        }else{
            if(count($showSome) >= 1){
                $search = auth()->user()->tag()->pluck('id')->toArray();
            }else{
                $search = Tag::latest()->pluck('id')->toArray();
            }
        }
        if ($request->date){
            if (count($showSome) >= 1){
                $date = auth()->user()->tag()->whereDate('created_at',$request->date)->pluck('id')->toArray();
            }else{
                $date = Tag::whereDate('created_at',$request->date)->pluck('id')->toArray();
            }
        }else{
            if(count($showSome) >= 1){
                $date = auth()->user()->tag()->pluck('id')->toArray();
            }else{
                $date = Tag::latest()->pluck('id')->toArray();
            }
        }

        $arrayFilter = array_intersect($search,$date);
        $taxes = Tag::latest()->whereIn('id' , $arrayFilter)->paginate(30);
        $taxesSend = Tag::latest()->pluck('name','id');
        $name='برچسب';
        $routeAddress='tag';
        $sidebar= '4';
        $labels = ['#','آیدی','نام','پیوند','تاریخ ثبت','عملیات'];
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