<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchCat(Request $request){
        return Category::where("name" , "LIKE" , "%{$request->search}%")->get();
    }
    public function searchBrand(Request $request){
        return Brand::where("name" , "LIKE" , "%{$request->search}%")->get();
    }
    public function searchAdvance(Request $request){
        if ($request->range){
            $range = Post::where('price', '>=', $request->range[0])->where('status' , 1)->where('price', '<=', $request->range[1])->pluck('id')->toArray();
        }else{
            $range = Post::where('status' , 1)->pluck('id')->toArray();
        }
        if ($request->postOff){
            $off = Post::where('status' , 1)->where('off', $request->postOff)->pluck('id')->toArray();
        }else{
            $off = Post::where('status' , 1)->pluck('id')->toArray();
        }
        if ($request->postId){
            $product_id = Post::where('status' , 1)->where('product_id', $request->postId)->pluck('id')->toArray();
        }else{
            $product_id = Post::where('status' , 1)->pluck('id')->toArray();
        }
        if ($request->postName){
            $title = Post::where('status' , 1)->where("title" , "LIKE" , "%{$request->postName}%")->pluck('id')->toArray();
        }else{
            $title = Post::where('status' , 1)->pluck('id')->toArray();
        }
        if ($request->allCat){
            $category = [];
            for ( $i = 0; $i < count($request->allCat); $i++) {
                $send = Category::where('name' , $request->allCat[$i]['name'])->with(["post" => function($q){
                    $q->where('status' , 1)->latest();
                }])->first();
                for ( $i2 = 0; $i2 < count($send['post']); $i2++) {
                    array_push($category ,$send['post'][$i2]['id']);
                }
            }
        }else{
            $category = Post::pluck('id')->toArray();
        }
        if ($request->allBrand){
            $brand = [];
            for ( $i = 0; $i < count($request->allBrand); $i++) {
                $send = Brand::where('name' , $request->allBrand[$i]['name'])->with(["post" => function($q){
                    $q->where('status' , 1)->latest();
                }])->first();
                for ( $i2 = 0; $i2 < count($send['post']); $i2++) {
                    array_push($brand ,$send['post'][$i2]['id']);
                }
            }
        }else{
            $brand = Post::where('status' , 1)->pluck('id')->toArray();
        }
        $arraySearch = array_intersect($range, $off,$title,$category,$brand,$product_id);

        return $postsSearch = Post::whereIn('id' , $arraySearch)->where('status', 1)->get();
    }
}
