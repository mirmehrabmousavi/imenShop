<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request){
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش دسته های خودش')->pluck('name');
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش دسته')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف دسته')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن دسته')->pluck('name');
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
                $tax = Category::where('id', $value)->first();
                $tax->user()->detach();
                $tax->post()->detach();
            }
            DB::table('categories')->whereIn('id', $request->value)->delete();
        }
        if($request->name){
            $request->validate([
                'name' => 'required|max:220',
            ]);
            if($request->taxId){
                $tax = Category::where('id' , $request->taxId)->first();
                $tax->update([
                    'name'=> $request->name,
                    'nameEn'=> $request->nameEn,
                    'image'=> $request->image,
                    'type'=> $request->status,
                    'slug'=> $request->slug,
                    'updated_at'=> Carbon::now(),
                ]);
                $tax->cats()->detach();
                if($request->allCategory){
                    foreach ($request->allCategory as $value) {
                        $categories = Category::latest()->where('id' ,  $value)->first();
                        $tax->cats()->attach($categories->id);
                    }
                }
            }else{
                $tax = Category::where('name' , $request->name)->first();
                if (!$tax){
                    $tax = Category::create([
                        'name'=> $request->name,
                        'nameEn'=> $request->nameEn,
                        'image'=> $request->image,
                        'type'=> $request->status,
                        'slug'=> $request->slug,
                    ]);
                    if($request->allCategory){
                        foreach ($request->allCategory as $value) {
                            $categories = Category::latest()->where('id' ,  $value)->first();
                            $tax->cats()->attach($categories->id);
                        }
                    }
                    auth()->user()->category()->sync($tax->id);
                }
            }
        }
        if($request->taxId && !$request->name){
            $taxEdit = Category::where('id' , $request->taxId)->first();
            $categories = $taxEdit->cats;
        }else{
            $taxEdit = '';
            $categories = [];
        }


        if ($request->search){
            if (count($showSome) >= 1){
                $search = auth()->user()->category()->where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = auth()->user()->category()->where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }else{
                $search = Category::where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = Category::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }
        }else{
            if(count($showSome) >= 1){
                $search = auth()->user()->category()->pluck('id')->toArray();
            }else{
                $search = Category::latest()->pluck('id')->toArray();
            }
        }
        if ($request->date){
            if (count($showSome) >= 1){
                $date = auth()->user()->category()->whereDate('created_at',$request->date)->pluck('id')->toArray();
            }else{
                $date = Category::whereDate('created_at',$request->date)->pluck('id')->toArray();
            }
        }else{
            if(count($showSome) >= 1){
                $date = auth()->user()->category()->pluck('id')->toArray();
            }else{
                $date = Category::latest()->pluck('id')->toArray();
            }
        }

        $arrayFilter = array_intersect($search,$date);
        $taxes = Category::latest()->whereIn('id' , $arrayFilter)->paginate(30);
        $taxesSend = Category::latest()->pluck('name','id');
        $name='دسته بندی';
        $routeAddress='category';
        $sidebar= '4';
        $labels = ['#','آیدی','نام','پیوند','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('AllTaxonami' , [
            'name' => $name,
            'taxes' => $taxes,
            'categories'=> $categories,
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
