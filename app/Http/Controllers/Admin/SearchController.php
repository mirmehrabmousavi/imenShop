<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Guarantee;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Time;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchGallery(Request $request){
        return Gallery::where("name" , "LIKE" , "%{$request->search}%")->get();
    }
    public function product(Request $request){
        return Post::where("title" , "LIKE" , "%{$request->search}%")->where('variety' , '0')->where('type' , 0)->with('review','guarantee')->get();
    }
    public function tax(Request $request){
        if($request->taxRoute == 'دسته بندی'){
            return Category::where("name" , "LIKE" , "%{$request->search}%")->pluck('name' , 'id');
        }
        if($request->taxRoute == 'برند'){
            return Brand::where("name" , "LIKE" , "%{$request->search}%")->pluck('name' , 'id');
        }
        if($request->taxRoute == 'برچسب'){
            return Tag::where("name" , "LIKE" , "%{$request->search}%")->pluck('name' , 'id');
        }
        if($request->taxRoute == 'بازه زمانی'){
            return Time::where("name" , "LIKE" , "%{$request->search}%")->pluck('name' , 'id');
        }
        if($request->taxRoute == 'گارانتی'){
            return Guarantee::where("name" , "LIKE" , "%{$request->search}%")->pluck('name' , 'id');
        }
        if($request->taxRoute == 'کاربر'){
            return User::where("name" , "LIKE" , "%{$request->search}%")->select(['name' , 'id'])->get();
        }
    }
    public function createTax(Request $request){
        if($request->taxRoute == 'برند'){
            $tax = Brand::where('name' , $request->tax)->first();
            if (!$tax){
                $tax = Brand::create([
                    'name'=> $request->tax,
                ]);
                auth()->user()->brand()->sync($tax->id);
                return $tax;
            }else{
                return 'exist';
            }
        }
        if($request->taxRoute == 'دسته بندی'){
            $tax = Category::where('name' , $request->tax)->first();
            if (!$tax){
                $tax = Category::create([
                    'name'=> $request->tax,
                ]);
                auth()->user()->category()->sync($tax->id);
                return $tax;
            }else{
                return 'exist';
            }
        }
        if($request->taxRoute == 'برچسب'){
            $tax = Tag::where('name' , $request->tax)->first();
            if (!$tax){
                $tax = Tag::create([
                    'name'=> $request->tax,
                ]);
                auth()->user()->tag()->sync($tax->id);
                return $tax;
            }else{
                return 'exist';
            }
        }
        if($request->taxRoute == 'بازه زمانی'){
            $tax = Time::where('name' , $request->tax)->first();
            if (!$tax){
                $tax = Time::create([
                    'name'=> $request->tax,
                ]);
                return $tax;
            }else{
                return 'exist';
            }
        }
        if($request->taxRoute == 'گارانتی'){
            $tax = Guarantee::where('name' , $request->tax)->first();
            if (!$tax){
                $tax = Guarantee::create([
                    'name'=> $request->tax,
                ]);
                return $tax;
            }else{
                return 'exist';
            }
        }
    }
}
