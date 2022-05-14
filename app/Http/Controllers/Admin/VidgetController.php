<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Vidget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VidgetController extends Controller
{
    public function template(Request $request){
        if($request->update){
            DB::table('vidgets')->delete();
            foreach ($request->allSiteTemplate as $item){
                if($item['brand'] != '' && $item['name'] != 'تبلیغات ساده' && $item['name'] != 'پیشنهاد شگفت انگیز' && $item['name'] != 'اسلایدر بزرگ تبلیغ' && $item['name'] != 'تبلیغات اسلایدری'){
                    $brand = implode(',' , $item['brand']);
                }else{
                    $brand = '';
                }
                if($item['category'] != '' && $item['name'] != 'تبلیغات ساده' && $item['name'] != 'اسلایدر بزرگ تبلیغ' && $item['name'] != 'تبلیغات اسلایدری'){
                    $category = implode(',' , $item['category']);
                }else{
                    $category = '';
                }
                if($item['name'] == 'تبلیغات ساده' || $item['name'] == 'اسلایدر بزرگ تبلیغ'){
                    Vidget::create([
                        'name'=> $item['name'],
                        'brand'=> $item['brand'],
                        'more'=> $item['more'],
                        'show'=> $item['show'],
                        'slug'=> $item['name'],
                        'type'=> $item['type'],
                        'count'=> $item['count'],
                        'platform'=> 0,
                    ]);
                }
                elseif ($item['name'] == 'تبلیغات اسلایدری'){
                    Vidget::create([
                        'name'=> $item['name'],
                        'slug'=> $item['name'],
                        'brand'=> $item['brand'],
                        'category'=> $item['category'],
                        'more'=> $item['more'],
                        'show'=> $item['show'],
                        'type'=> $item['type'],
                        'count'=> $item['count'],
                        'platform'=> 0,
                    ]);
                }
                elseif ($item['name'] == 'پیشنهاد شگفت انگیز'){
                    if ($item['slug']){
                        $slug = $item['slug'];
                    }else{
                        $slug = 'پیشنهاد-شگفت-انگیز';
                    }
                    Vidget::create([
                        'name'=> $item['name'],
                        'title'=> $item['title'],
                        'moreEn'=> $item['moreEn'],
                        'brand'=> $item['brand'],
                        'more'=> $item['more'],
                        'show'=> $item['show'],
                        'type'=> $item['type'],
                        'platform'=> 0,
                        'slug'=> $slug,
                        'count'=> $item['count'],
                        'category'=> $category,
                    ]);
                }
                elseif ($item['name'] == 'پست ویژه با تصویر'){
                    Vidget::create([
                        'name'=> $item['name'],
                        'title'=> $item['title'],
                        'more'=> $item['more'],
                        'titleEn'=> $item['titleEn'],
                        'moreEn'=> $item['moreEn'],
                        'background'=> $item['background'],
                        'show'=> $item['show'],
                        'type'=> $item['type'],
                        'count'=> $item['count'],
                        'slug'=> $item['name'],
                        'category'=> $category,
                        'platform'=> 0,
                        'brand'=> $brand,
                    ]);
                }
                else{
                    Vidget::create([
                        'name'=> $item['name'],
                        'title'=> $item['title'],
                        'more'=> $item['more'],
                        'titleEn'=> $item['titleEn'],
                        'moreEn'=> $item['moreEn'],
                        'background'=> $item['background'],
                        'show'=> $item['show'],
                        'type'=> $item['type'],
                        'count'=> $item['count'],
                        'slug'=> $item['slug'],
                        'category'=> $category,
                        'platform'=> 0,
                        'brand'=> $brand,
                    ]);
                }
            }
            foreach ($request->allSiteTemplateApp as $item){
                if($item['brand'] != ''){
                    $brand = implode(',' , $item['brand']);
                }else{
                    $brand = '';
                }
                if($item['category'] != ''){
                    $category = implode(',' , $item['category']);
                }else{
                    $category = '';
                }
                if ($item['name'] == 'پست ویژه با تصویر'){
                    Vidget::create([
                        'name'=> $item['name'],
                        'title'=> $item['title'],
                        'more'=> $item['more'],
                        'titleEn'=> $item['titleEn'],
                        'moreEn'=> $item['moreEn'],
                        'background'=> $item['background'],
                        'show'=> $item['show'],
                        'type'=> $item['type'],
                        'count'=> $item['count'],
                        'slug'=> $item['name'],
                        'category'=> $category,
                        'brand'=> $brand,
                        'platform'=> 1,
                    ]);
                }
                else{
                    Vidget::create([
                        'name'=> $item['name'],
                        'title'=> $item['title'],
                        'more'=> $item['more'],
                        'titleEn'=> $item['titleEn'],
                        'moreEn'=> $item['moreEn'],
                        'background'=> $item['background'],
                        'show'=> $item['show'],
                        'type'=> $item['type'],
                        'count'=> $item['count'],
                        'slug'=> $item['slug'],
                        'platform'=> 1,
                        'category'=> $category,
                        'brand'=> $brand,
                    ]);
                }
            }
            foreach ($request->allSiteTemplateSingle as $item){
                Vidget::create([
                    'name'=> $item['name'],
                    'platform'=> 2,
                ]);
            }
        }
        $category = Category::latest()->where('type' , 0)->get();
        $categoryNews = Category::latest()->where('type' , 1)->get();
        $brand = Brand::latest()->get();
        $vidgets = Vidget::where('platform' , 0)->get();
        $vidget = [];
        foreach ($vidgets as $item){
            $vidgetCategory = [
                'name'=> $item['name'],
                'title'=> $item['title'],
                'more'=> $item['more'],
                'show'=> $item['show'],
                'type'=> $item['type'],
                'count'=> $item['count'],
                'slug'=> $item['slug'],
                'titleEn'=> $item['titleEn'],
                'moreEn'=> $item['moreEn'],
                'background'=> $item['background'],
                'view'=> $item['view'],
                'category'=> [],
                'brand'=> [],
            ];
            if($item['brand'] != null){
                $vidgetCategory['brand'] = explode(',' , $item['brand']);
            }else{
                $vidgetCategory['brand'] = [];
            }
            if($item['category'] != null){
                $vidgetCategory['category'] = explode(',' , $item['category']);
            }else{
                $vidgetCategory['category'] = [];
            }
            array_push($vidget , $vidgetCategory);
        }

        $vidgetsApp = Vidget::where('platform' , 1)->get();
        $vidgetApp = [];
        foreach ($vidgetsApp as $item){
            $vidgetCategory = [
                'name'=> $item['name'],
                'title'=> $item['title'],
                'more'=> $item['more'],
                'show'=> $item['show'],
                'type'=> $item['type'],
                'count'=> $item['count'],
                'slug'=> $item['slug'],
                'titleEn'=> $item['titleEn'],
                'moreEn'=> $item['moreEn'],
                'background'=> $item['background'],
                'view'=> $item['view'],
                'category'=> [],
                'brand'=> [],
            ];
            if($item['brand'] != null){
                $vidgetCategory['brand'] = explode(',' , $item['brand']);
            }else{
                $vidgetCategory['brand'] = [];
            }
            if($item['category'] != null){
                $vidgetCategory['category'] = explode(',' , $item['category']);
            }else{
                $vidgetCategory['category'] = [];
            }
            array_push($vidgetApp , $vidgetCategory);
        }

        $vidgetSingles = Vidget::where('platform' , 2)->get();
        $vidgetSingle = [];
        foreach ($vidgetSingles as $item){
            $vidgetCategory = [
                'name'=> $item['name'],
            ];
            array_push($vidgetSingle , $vidgetCategory);
        }
        Inertia::setRootView('admin');
        return Inertia::render('SettingDesign',[
            'vidget' => $vidget,
            'vidgetApp' => $vidgetApp,
            'vidgetSingle' => $vidgetSingle,
            'category' => $category,
            'categoryNews' => $categoryNews,
            'brand' => $brand,
        ]);
    }
}
