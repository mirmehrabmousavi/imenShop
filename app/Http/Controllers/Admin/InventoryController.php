<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index(Request $request){
        if ($request->sort){
            $products = Post::latest()->with('review')->where('type' , 0)->get();
            $product = [];
            foreach ($products as $item){
                if ($item['review'][0]['colors'] != '[]'){
                    foreach (json_decode($item['review'][0]['colors'] , true) as $color){
                        if ($request->sort == 1){
                            if($color['count'] >= 1){
                                array_push($product , $item['id']);
                            }
                        }else{
                            if($color['count'] <= 0){
                                array_push($product , $item['id']);
                            }
                        }
                    }
                }
                if ($item['review'][0]['size'] != '[]'){
                    foreach (json_decode($item['review'][0]['size'] , true) as $size){
                        if ($request->sort == 1){
                            if($size['count'] >= 1){
                                array_push($product , $item['id']);
                            }
                        }else{
                            if($size['count'] <= 0){
                                array_push($product , $item['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $product = Post::latest()->where('type' , 0)->pluck('id')->toArray();
        }

        if ($request->getPage){
            $page = $request->getPage;
        }else{
            $page = '25';
        }

        if ($request->category){
            $categoryName = Category::latest()->where('name' , $request->category)->first();
            $category = $categoryName->post()->where('type' , 0)->pluck('id')->toArray();
        }else{
            $category = Post::latest()->where('type' , 0)->pluck('id')->toArray();
        }

        if ($request->search){
            if ($request->date){
                $posts = Post::latest()->with('review')->where('type' , 0)->whereIn('id',$category)->whereIn('id',$product)->whereDate('created_at',$request->date)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                if (count($posts) == 0){
                    $posts = Post::latest()->with('review')->where('type' , 0)->whereIn('id',$category)->whereIn('id',$product)->whereDate('created_at',$request->date)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                }
            }else{
                $posts = Post::latest()->with('review')->where('type' , 0)->whereIn('id',$category)->whereIn('id',$product)->where("title" , "LIKE" , "%{$request->search}%")->paginate($page);
                if (count($posts) == 0){
                    $posts = Post::latest()->with('review')->where('type' , 0)->whereIn('id',$category)->whereIn('id',$product)->where("id" , "LIKE" , "%{$request->search}%")->paginate($page);
                }
            }
        }else{
            if ($request->date){
                $posts = Post::latest()->with('review')->where('type' , 0)->whereDate('created_at',$request->date)->whereIn('id',$category)->whereIn('id',$product)->paginate($page);
            }else{
                $posts = Post::latest()->with('review')->where('type' , 0)->whereIn('id',$category)->whereIn('id',$product)->paginate($page);
            }
        }
        Inertia::setRootView('admin');
        return Inertia::render('InventoryIndex' , [
            'posts' => $posts
        ]);
    }
}
