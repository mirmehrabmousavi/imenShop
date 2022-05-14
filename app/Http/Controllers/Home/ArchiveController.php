<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\News;
use App\Models\Post;
use App\Models\Rank;
use App\Models\Score;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\Vidget;
use App\Traits\SeoHelper;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArchiveController extends Controller
{
    use SeoHelper;
    public function category(Request $request , Category $category){
        $name = Category::where('id' , $category->id)->pluck('name')->first();
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first() ?:'' ;
        $shortActivity = Setting::where('key' , 'descriptionSeo')->pluck('value')->first() ?:'' ;
        $this->seoSingleSeo(   $category->name . " - $title " , $shortActivity , 'store' , 'archive/category'."$category->slug" , $logo );
        $url = 'category/';
        $maxPrice = $category->post()->where('status' , 1)->orderBy('price','DESC')->pluck('price')->first();
        $minPrice = $category->post()->where('status' , 1)->orderBy('price')->pluck('price')->first();

        $post = $category->post()->where('status' , 1)->with('review')->get();
        $color1 = [];
        for ( $i = 0; $i < count($post); $i++) {
            $colors = json_decode($post[$i]['review'][0]['colors']);
            if ($colors != null){
                for ( $i2 = 0; $i2 < count($colors); $i2++) {
                    array_push($color1 , $colors[$i2]->name);
                }
            }
        }
        $color = array_unique($color1);

        $size1 = [];
        for ( $i = 0; $i < count($post); $i++) {
            $sizes = json_decode($post[$i]['review'][0]['size'] , true);
            if ($sizes != null){
                for ( $i2 = 0; $i2 < count($sizes); $i2++) {
                    array_push($size1 , $sizes[$i2]['name']);
                }
            }
        }
        $size = array_unique($size1);

        $ability = [];
        $check = 'no';
        for ( $i = 0; $i < count($post); $i++) {
            $abilities = json_decode($post[$i]['review'][0]['ability'] , true);
            if ($abilities != null){
                for ( $i2 = 0; $i2 < count($abilities); $i2++) {
                    for ( $i3 = 0; $i3 < count($ability); $i3++) {
                        if ($ability[$i3] == $abilities[$i2]){
                            $check = 'yes';
                        }
                    }
                    if ($check == 'no'){
                        array_push($ability , $abilities[$i2]);
                    }
                    $check = 'no';
                }
            }
        }

        $off = [];
        $check = 'no';
        for ( $i = 0; $i < count($post); $i++) {
            $offs = json_decode($post[$i]['off']);
            if ($offs != null){
                for ( $i2 = 0; $i2 < count($off); $i2++) {
                    if ($off[$i2] == $offs){
                        $check = 'yes';
                    }
                }
                if ($check == 'no'){
                    array_push($off , $offs);
                }
                $check = 'no';
            }
        }
        $brands = Brand::latest()->get();
        $catsTop = [];
        $brandTop= [];

        $cats = Category::where('id' , $category->id)->with(["cats" => function($q){
            $q->latest()
                ->with(["post" => function($q){
                    $q->latest()->where('status',1);
                }])
                ->with(["cats" => function($q){
                    $q->latest()->with(["cats" => function($q){
                        $q->latest()->with('cats');
                    }]);
                }]);
        }])->get();

        foreach($cats[0]['cats'] as $item){
            $send = Category::where('id' , $item->id)->with(["post" => function($q){
                $q->latest()->where('status',1)->take(1);
            }])->first();
            array_push($catsTop ,$send);
        }

        foreach($brands as $item){
            $send = Brand::where('id' , $item->id)->with(["post" => function($q){
                $q->latest()->where('status',1)->take(1);
            }])->first();
            array_push($brandTop ,$send);
        }

        if ($request->allAbility){
            $abilityId = [];
            for ( $i = 0; $i < count($post); $i++) {
                $abilitiesIds = json_decode($post[$i]['review'][0]['ability'],true);
                if ($abilitiesIds != null){
                    for ( $i2 = 0; $i2 < count($abilitiesIds); $i2++) {
                        for ( $i3 = 0; $i3 < count($request->allAbility); $i3++) {
                            if ($request->allAbility[$i3] == $abilitiesIds[$i2]['name']){
                                array_push($abilityId , $post[$i]['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $abilityId = $category->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allSize){
            $sizeId = [];
            for ( $i = 0; $i < count($post); $i++) {
                $sizeIds = json_decode($post[$i]['review'][0]['size'] , true);
                if ($sizeIds != null){
                    for ( $i2 = 0; $i2 < count($sizeIds); $i2++) {
                        for ( $i3 = 0; $i3 < count($request->allSize); $i3++) {
                            if ($request->allSize[$i3] == $sizeIds[$i2]->name){
                                array_push($sizeId , $post[$i]['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $sizeId = $category->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->search){
            $searchId = $category->post()->latest()->where("title" , "LIKE" , "%{$request->search}%")->where('status' , 1)->pluck('id')->toArray();
        }else{
            $searchId = $category->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allColor){
            $colorId = [];
            for ( $i = 0; $i < count($post); $i++) {
                $colorIds = json_decode($post[$i]['review'][0]['colors'] , true);
                if ($colorIds != null){
                    for ( $i2 = 0; $i2 < count($colorIds); $i2++) {
                        for ( $i3 = 0; $i3 < count($request->allColor); $i3++) {
                            if ($request->allColor[$i3] == $colorIds[$i2]['name']){
                                array_push($colorId , $post[$i]['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $colorId = $category->post()->where('status' , 1)->pluck('id')->toArray();
        }
        if ($request->max){
            $rangeId = $category->post()->where('price', '>=', $request->min)->where('status' , 1)->where('price', '<=', $request->max)->pluck('id')->toArray();
        }else{
            $rangeId = $category->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allOff){
            $offId = $category->post()->whereIn('off' , $request->allOff)->where('status' , 1)->pluck('id')->toArray();
        }else{
            $offId = $category->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->suggest){
            $suggestId = $category->post()->where('suggest' , '!=' , null)->where('status' , 1)->pluck('id')->toArray();
        }else{
            $suggestId = $category->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->count){
            $countId = $category->post()->where('count' , '!=' , '0')->where('status' , 1)->pluck('id')->toArray();
        }else{
            $countId = $category->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allBrands){
            $brandId1 = [];
            $brandId = [];
            for ( $i = 0; $i < count($request->allBrands); $i++) {
                $brandCheck1 = Brand::where('name', $request->allBrands[$i])->first();
                $brandCheck = $brandCheck1->post()->pluck('id');
                array_push($brandId1 , $brandCheck);
            }
            for ( $i = 0; $i < count($brandId1[0]); $i++) {
                $send = Post::where('id' , $brandId1[0][$i])->pluck('id')->first();
                array_push($brandId , $send);
            }
        }else{
            $brandId = $category->post()->where('status' , 1)->pluck('id')->toArray();
        }

        $arrayFilter = array_intersect($brandId, $offId,$rangeId,$colorId,$searchId,$countId,$suggestId,$sizeId,$abilityId);

        $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();

        if ($request->show == 0){
            $catPost1 = $category->post()->where('variety' , 0)->latest()->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        if ($request->show == 2){
            $catPost1 = $category->post()->where('variety' , 0)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        if ($request->show == 1 or $request->show == 3){
            $catPost1 = $category->post()->where('variety' , 0)->withCount('view')->orderBy('view_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        if ($request->show == 4){
            $catPost1 = $category->post()->where('variety' , 0)->orderBy('price')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        if ($request->show == 5){
            $catPost1 = $category->post()->where('variety' , 0)->orderBy('price','DESC')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        $catPost = ProductResource::collection($catPost1);
        $showcase = $category->post()->where('variety' , 0)->where('showcase' , 1)->where('status' , 1)->take(5)->get();
        return Inertia::render('Archive/ProductArchive' , [
            'cats' => $cats,
            'catPost' => $catPost,
            'showcase' => $showcase,
            'title' => $title,
            'minPrice' => $minPrice,
            'catsTop' => $catsTop,
            'brandTop' => $brandTop,
            'url' => $url,
            'maxPrice' => $maxPrice,
            'off' => $off,
            'size' => $size,
            'ability' => $ability,
            'brands' => $brands,
            'color' => $color,
        ]);
    }

    public function brand(Request $request , Brand $brand){
        $name = Category::where('id' , $brand->id)->pluck('name')->first();
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first() ?:'' ;
        $shortActivity = Setting::where('key' , 'descriptionSeo')->pluck('value')->first() ?:'' ;
        $this->seoSingleSeo(   $brand->name . " - $title " , $shortActivity , 'store' , 'archive/brand'."$brand->slug" , $logo );

        $url = 'brand/';
        $maxPrice = $brand->post()->where('status' , 1)->orderBy('price','DESC')->pluck('price')->first();
        $minPrice = $brand->post()->where('status' , 1)->orderBy('price')->pluck('price')->first();

        $post = $brand->post()->where('status' , 1)->with('review')->get();
        $color1 = [];
        for ( $i = 0; $i < count($post); $i++) {
            $colors = json_decode($post[$i]['review'][0]['colors']);
            if ($colors != null){
                for ( $i2 = 0; $i2 < count($colors); $i2++) {
                    array_push($color1 , $colors[$i2]->name);
                }
            }
        }
        $color = array_unique($color1);

        $size1 = [];
        for ( $i = 0; $i < count($post); $i++) {
            $sizes = json_decode($post[$i]['review'][0]['size'] , true);
            if ($sizes != null){
                for ( $i2 = 0; $i2 < count($sizes); $i2++) {
                    array_push($size1 , $sizes[$i2]['name']);
                }
            }
        }
        $size = array_unique($size1);

        $ability = [];
        $check = 'no';
        for ( $i = 0; $i < count($post); $i++) {
            $abilities = json_decode($post[$i]['review'][0]['ability']);
            if ($abilities != null){
                for ( $i2 = 0; $i2 < count($abilities); $i2++) {
                    for ( $i3 = 0; $i3 < count($ability); $i3++) {
                        if ($ability[$i3] == $abilities[$i2]){
                            $check = 'yes';
                        }
                    }
                    if ($check == 'no'){
                        array_push($ability , $abilities[$i2]);
                    }
                    $check = 'no';
                }
            }
        }

        $off = [];
        $check = 'no';
        for ( $i = 0; $i < count($post); $i++) {
            $offs = json_decode($post[$i]['off']);
            if ($offs != null){
                for ( $i2 = 0; $i2 < count($off); $i2++) {
                    if ($off[$i2] == $offs){
                        $check = 'yes';
                    }
                }
                if ($check == 'no'){
                    array_push($off , $offs);
                }
                $check = 'no';
            }
        }
        $brands = [];
        $cats = collect([['slug' => $brand->slug ,'name' => $brand->name , 'cats'=> []]]);
        $brands1 = Brand::latest()->get();

        $catsTop = [];
        $brandTop = [];
        foreach($brands1 as $item){
            $send = Brand::where('id' , $item->id)->with(["post" => function($q){
                $q->latest()->where('status',1)->take(1);
            }])->first();
            array_push($brandTop ,$send);
        }

        if ($request->allAbility){
            $abilityId = [];
            for ( $i = 0; $i < count($post); $i++) {
                $abilitiesId = json_decode($post[$i]['review'][0]['ability']);
                if ($abilitiesId != null){
                    for ( $i2 = 0; $i2 < count($abilitiesId); $i2++) {
                        for ( $i3 = 0; $i3 < count($request->allAbility); $i3++) {
                            if ($request->allAbility[$i3] == $abilitiesId[$i2]){
                                array_push($abilityId , $post[$i]['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $abilityId = $brand->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allSize){
            $sizeId = [];
            for ( $i = 0; $i < count($post); $i++) {
                $sizesId = json_decode($post[$i]['review'][0]['size']);
                if ($sizesId != null){
                    for ( $i2 = 0; $i2 < count($sizesId); $i2++) {
                        for ( $i3 = 0; $i3 < count($request->allSize); $i3++) {
                            if ($request->allSize[$i3] == $sizesId[$i2]->name){
                                array_push($sizeId , $post[$i]['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $sizeId = $brand->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->search){
            $searchId = $brand->post()->latest()->where("title" , "LIKE" , "%{$request->search}%")->where('status' , 1)->pluck('id')->toArray();
        }else{
            $searchId = $brand->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allColor){
            $colorId = [];
            for ( $i = 0; $i < count($post); $i++) {
                $colorsId = $post[$i]['review'][0]['colors'];
                if ($colorsId != null){
                    $get2 = explode(',' , $colorsId);
                    for ( $i2 = 0; $i2 < count($get2); $i2++) {
                        $get3 = explode('-' , $get2[$i2]);
                        for ( $i3 = 0; $i3 < count($request->allColor); $i3++) {
                            if ($request->allColor[$i3] == $get3[0]){
                                array_push($colorId , $post[$i]['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $colorId = $brand->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->max){
            $rangeId = $brand->post()->where('price', '>=', $request->min)->where('status' , 1)->where('price', '<=', $request->max)->pluck('id')->toArray();
        }else{
            $rangeId = $brand->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allOff){
            $offId = $brand->post()->whereIn('off' , $request->allOff)->where('status' , 1)->pluck('id')->toArray();
        }else{
            $offId = $brand->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->suggest){
            $suggestId = $brand->post()->where('suggest' , '!=' , null)->where('status' , 1)->pluck('id')->toArray();
        }else{
            $suggestId = $brand->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->count){
            $countId = $brand->post()->where('count' , '!=' , '0')->where('status' , 1)->pluck('id')->toArray();
        }else{
            $countId = $brand->post()->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allBrands){
            $brandId1 = [];
            $brandId = [];
            for ( $i = 0; $i < count($request->allBrands); $i++) {
                $brandCheck1 = Brand::where('name', $request->allBrands[$i])->first();
                $brandCheck = $brandCheck1->post()->pluck('id');
                array_push($brandId1 , $brandCheck);
            }
            for ( $i = 0; $i < count($brandId1[0]); $i++) {
                $send = Post::where('id' , $brandId1[0][$i])->pluck('id')->first();
                array_push($brandId , $send);
            }
        }else{
            $brandId = $brand->post()->where('status' , 1)->pluck('id')->toArray();
        }

        $arrayFilter = array_intersect($brandId, $offId,$rangeId,$colorId,$searchId,$countId,$suggestId,$sizeId,$abilityId);
        $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();

        if ($request->show == 0){
            $catPost = $brand->post()->where('variety' , 0)->latest()->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        if ($request->show == 2){
            $catPost = $brand->post()->where('variety' , 0)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        if ($request->show == 1 or $request->show == 3){
            $catPost = $brand->post()->where('variety' , 0)->withCount('view')->orderBy('view_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        if ($request->show == 4){
            $catPost = $brand->post()->where('variety' , 0)->orderBy('price')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        if ($request->show == 5){
            $catPost = $brand->post()->where('variety' , 0)->orderBy('price','DESC')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        $showcase = $brand->post()->where('variety' , 0)->where('showcase' , 1)->where('status' , 1)->take(5)->get();
        return Inertia::render('Archive/ProductArchive' , [
            'showcase' => $showcase,
            'cats' => $cats,
            'url' => $url,
            'title' => $title,
            'catPost' => $catPost,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'catsTop' => $catsTop,
            'brandTop' => $brandTop,
            'off' => $off,
            'size' => $size,
            'ability' => $ability,
            'brands' => $brands,
            'color' => $color,
        ]);
    }

    public function search(Request $request){
        $search2 = $request->search;
        if(is_array($search2)){
            $search1 = $search2[0];
        }else{
            $search1 = $search2;
        }
        $address = Setting::where('key' , 'address')->pluck('value')->first() ?:'' ;
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first() ?:'' ;
        $body = Setting::where('key' , 'descriptionSeo')->pluck('value')->first() ?:'' ;
        $this->seoSingleSeo(   $search1 . " - $title " , $body , 'store' , "archive/search?search=".$search1 , $logo );

        $url = 'search?search='.$search1;
        $maxPrice = Post::where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->orderBy('price','DESC')->pluck('price')->first();
        $minPrice = Post::where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->orderBy('price')->pluck('price')->first();

        $post = Post::where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->with('review')->get();
        $color1 = [];
        for ( $i = 0; $i < count($post); $i++) {
            $colors = json_decode($post[$i]['review'][0]['colors']);
            if ($colors != null){
                for ( $i2 = 0; $i2 < count($colors); $i2++) {
                    array_push($color1 , $colors[$i2]->name);
                }
            }
        }
        $color = array_unique($color1);

        $size1 = [];
        for ( $i = 0; $i < count($post); $i++) {
            $sizes = json_decode($post[$i]['review'][0]['size'] , true);
            if ($sizes != null){
                for ( $i2 = 0; $i2 < count($sizes); $i2++) {
                    array_push($size1 , $sizes[$i2]['name']);
                }
            }
        }
        $size = array_unique($size1);

        $ability = [];
        $check = 'no';
        for ( $i = 0; $i < count($post); $i++) {
            $abilities = json_decode($post[$i]['review'][0]['ability']);
            if ($abilities != null){
                for ( $i2 = 0; $i2 < count($abilities); $i2++) {
                    for ( $i3 = 0; $i3 < count($ability); $i3++) {
                        if ($ability[$i3] == $abilities[$i2]){
                            $check = 'yes';
                        }
                    }
                    if ($check == 'no'){
                        array_push($ability , $abilities[$i2]);
                    }
                    $check = 'no';
                }
            }
        }

        $off = [];
        $check = 'no';
        for ( $i = 0; $i < count($post); $i++) {
            $offs = json_decode($post[$i]['off']);
            if ($offs != null){
                for ( $i2 = 0; $i2 < count($off); $i2++) {
                    if ($off[$i2] == $offs){
                        $check = 'yes';
                    }
                }
                if ($check == 'no'){
                    array_push($off , $offs);
                }
                $check = 'no';
            }
        }

        $brands = Brand::latest()->get();
        $cats = collect([['slug' => $search1,'name' => $search1 , 'cats'=> []]]);

        $catsTop = [];
        $brandTop = [];
        foreach($brands as $item){
            $send = Brand::where('id' , $item->id)->with(["post" => function($q){
                $q->latest()->where('status',1)->take(1);
            }])->first();
            array_push($brandTop ,$send);
        }
        if ($request->allAbility){
            $abilityId = [];
            for ( $i = 0; $i < count($post); $i++) {
                $abilitiesId = json_decode($post[$i]['review'][0]['ability']);
                if ($abilitiesId != null){
                    for ( $i2 = 0; $i2 < count($abilitiesId); $i2++) {
                        for ( $i3 = 0; $i3 < count($request->allAbility); $i3++) {
                            if ($request->allAbility[$i3] == $abilitiesId[$i2]){
                                array_push($abilityId , $post[$i]['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $abilityId = Post::where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allSize){
            $sizeId = [];
            for ( $i = 0; $i < count($post); $i++) {
                $sizesId = json_decode($post[$i]['review'][0]['size']);
                if ($sizesId != null){
                    for ( $i2 = 0; $i2 < count($sizesId); $i2++) {
                        for ( $i3 = 0; $i3 < count($request->allSize); $i3++) {
                            if ($request->allSize[$i3] == $sizesId[$i2]->name){
                                array_push($sizeId , $post[$i]['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $sizeId = Post::where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->pluck('id')->toArray();
        }

        if ($search1){
            $searchId = Post::where("title" , "LIKE" , "%{$search1}%")->latest()->where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->pluck('id')->toArray();
        }else{
            $searchId = Post::where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allColor){
            $colorId = [];
            for ( $i = 0; $i < count($post); $i++) {
                $colorsId = $post[$i]['review'][0]['colors'];
                if ($colorsId != null){
                    $get2 = explode(',' , $colorsId);
                    for ( $i2 = 0; $i2 < count($get2); $i2++) {
                        $get3 = explode('-' , $get2[$i2]);
                        for ( $i3 = 0; $i3 < count($request->allColor); $i3++) {
                            if ($request->allColor[$i3] == $get3[0]){
                                array_push($colorId , $post[$i]['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $colorId = Post::where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->max){
            $rangeId = Post::where("title" , "LIKE" , "%{$search1}%")->where('price', '>=', $request->min)->where('status' , 1)->where('price', '<=', $request->max)->pluck('id')->toArray();
        }else{
            $rangeId = Post::where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allOff){
            $offId = Post::where("title" , "LIKE" , "%{$search1}%")->whereIn('off' , $request->allOff)->where('status' , 1)->pluck('id')->toArray();
        }else{
            $offId = Post::where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->suggest){
            $suggestId = Post::where("title" , "LIKE" , "%{$search1}%")->where('suggest' , '!=' , null)->where('status' , 1)->pluck('id')->toArray();
        }else{
            $suggestId = Post::where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->count){
            $countId = Post::where("title" , "LIKE" , "%{$search1}%")->where('count' , '!=' , '0')->where('status' , 1)->pluck('id')->toArray();
        }else{
            $countId = Post::where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allBrands){
            $brandId1 = [];
            $brandId = [];
            for ( $i = 0; $i < count($request->allBrands); $i++) {
                $brandCheck1 = Brand::where('name', $request->allBrands[$i])->first();
                $brandCheck = $brandCheck1->post()->pluck('id');
                array_push($brandId1 , $brandCheck);
            }
            for ( $i = 0; $i < count($brandId1[0]); $i++) {
                $send = Post::where('id' , $brandId1[0][$i])->pluck('id')->first();
                array_push($brandId , $send);
            }
        }else{
            $brandId = Post::where("title" , "LIKE" , "%{$search1}%")->where('status' , 1)->pluck('id')->toArray();
        }

        $arrayFilter = array_intersect($brandId, $offId,$rangeId,$colorId,$searchId,$countId,$suggestId,$sizeId,$abilityId);
        $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();

        if ($request->show == 0){
            $catPost1 = Post::where("title" , "LIKE" , "%{$search1}%")->where('variety' , 0)->latest()->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        if ($request->show == 2){
            $catPost1 = Post::where("title" , "LIKE" , "%{$search1}%")->where('variety' , 0)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        if ($request->show == 1 or $request->show == 3){
            $catPost1 = Post::where("title" , "LIKE" , "%{$search1}%")->where('variety' , 0)->withCount('view')->orderBy('view_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        if ($request->show == 4){
            $catPost1 = Post::where("title" , "LIKE" , "%{$search1}%")->where('variety' , 0)->orderBy('price')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        if ($request->show == 5){
            $catPost1 = Post::where("title" , "LIKE" , "%{$search1}%")->where('variety' , 0)->orderBy('price','DESC')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
        }
        $catPost = ProductResource::collection($catPost1);
        $showcase = Post::where("title" , "LIKE" , "%{$search1}%")->where('variety' , 0)->where('showcase' , 1)->latest()->where('status' , 1)->take(5)->get();
        return Inertia::render('Archive/ProductArchive' , [
            'cats' => $cats,
            'address' => $address,
            'showcase' => $showcase,
            'body' => $body,
            'url' => $url,
            'search1' => $search1,
            'catPost' => $catPost,
            'title' => $title,
            'catsTop' => $catsTop,
            'brandTop' => $brandTop,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'off' => $off,
            'size' => $size,
            'ability' => $ability,
            'brands' => $brands,
            'color' => $color,
        ]);
    }

    public function vidget(Request $request , Vidget $vidget){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first() ?:'' ;
        $shortActivity = Setting::where('key' , 'descriptionSeo')->pluck('value')->first() ?:'' ;
        $this->seoSingleSeo(    " $title " , $shortActivity , 'store' , "archive/products?".$vidget->slug , $logo );

        $ids = [];
        $ids2 = [];
        if($vidget['category'] != '' && $vidget['name'] != 'خبر ها' && $vidget['name'] != 'تبلیغات ساده' && $vidget['name'] != 'اسلایدر بزرگ تبلیغ' && $vidget['name'] != 'تبلیغات اسلایدری'){
            $allCatSite3 = explode(',' , $vidget['category']);
            foreach ($allCatSite3 as $value){
                $tax = Category::where('name' , $value)->first();
                $send2 = $tax->post()->pluck('id')->toArray();
                foreach ($send2 as $data){
                    array_push($ids ,$data);
                }
            }
        }
        if($vidget['brand'] != '' && $vidget['name'] != 'خبر ها' && $vidget['name'] != 'تبلیغات ساده' && $vidget['name'] != 'پیشنهاد شگفت انگیز' && $vidget['name'] != 'اسلایدر بزرگ تبلیغ' && $vidget['name'] != 'تبلیغات اسلایدری'){
            $allBrandSite3 = explode(',' , $vidget['brand']);
            foreach ($allBrandSite3 as $value){
                $tax = Brand::where('name' , $value)->first();
                $send2 = $tax->post()->pluck('id')->toArray();
                foreach ($send2 as $data){
                    array_push($ids2 ,$data);
                }
            }
        }
        if($vidget['category'] == ''){
            $ids = Post::pluck('id')->toArray();
        }
        if($vidget['brand'] == '' || $vidget['name'] == 'پیشنهاد شگفت انگیز'){
            $ids2 = Post::pluck('id')->toArray();
        }
        $id3 = array_intersect($ids,$ids2);
        $category = '';
        $url = 'products/';
        if($vidget['name'] == 'محصولات دانلودی'){
            $maxPrice = Post::whereIn('id',$id3)->where('type' , 1)->where('status' , 1)->orderBy('price','DESC')->pluck('price')->first();
            $minPrice = Post::whereIn('id',$id3)->where('type' , 1)->where('status' , 1)->orderBy('price')->pluck('price')->first();
        }else{
            $maxPrice = Post::whereIn('id',$id3)->where('type' , 0)->where('status' , 1)->orderBy('price','DESC')->pluck('price')->first();
            $minPrice = Post::whereIn('id',$id3)->where('type' , 0)->where('status' , 1)->orderBy('price')->pluck('price')->first();
        }

        if($vidget['name'] == 'محصولات دانلودی'){
            $post = Post::whereIn('id',$id3)->where('type' , 1)->where('status' , 1)->with('review')->get();
        }else{
            $post = Post::whereIn('id',$id3)->where('type' , 0)->with('review')->get();
        }
        $color1 = [];
        for ( $i = 0; $i < count($post); $i++) {
            $colors = json_decode($post[$i]['review'][0]['colors']);
            if ($colors != null){
                for ( $i2 = 0; $i2 < count($colors); $i2++) {
                    array_push($color1 , $colors[$i2]->name);
                }
            }
        }
        $color = array_unique($color1);

        $size1 = [];
        for ( $i = 0; $i < count($post); $i++) {
            $sizes = json_decode($post[$i]['review'][0]['size'] , true);
            if ($sizes != null){
                for ( $i2 = 0; $i2 < count($sizes); $i2++) {
                    array_push($size1 , $sizes[$i2]['name']);
                }
            }
        }
        $size = array_unique($size1);

        $ability = [];
        $check = 'no';
        for ( $i = 0; $i < count($post); $i++) {
            $abilities = json_decode($post[$i]['review'][0]['ability'] , true);
            if ($abilities != null){
                for ( $i2 = 0; $i2 < count($abilities); $i2++) {
                    for ( $i3 = 0; $i3 < count($ability); $i3++) {
                        if ($ability[$i3] == $abilities[$i2]){
                            $check = 'yes';
                        }
                    }
                    if ($check == 'no'){
                        array_push($ability , $abilities[$i2]);
                    }
                    $check = 'no';
                }
            }
        }

        $off = [];
        $check = 'no';
        for ( $i = 0; $i < count($post); $i++) {
            $offs = json_decode($post[$i]['off']);
            if ($offs != null){
                for ( $i2 = 0; $i2 < count($off); $i2++) {
                    if ($off[$i2] == $offs){
                        $check = 'yes';
                    }
                }
                if ($check == 'no'){
                    array_push($off , $offs);
                }
                $check = 'no';
            }
        }
        $brands = Brand::latest()->get();
        $catsTop = [];
        $brandTop= [];
        $cats = collect([['slug' => $vidget->slug ,'name' => str_replace('-' , ' ' , $vidget->slug) , 'cats'=> []]]);

        foreach($brands as $item){
            $send = Brand::where('id' , $item->id)->with(["post" => function($q){
                $q->latest()->where('status',1)->take(1);
            }])->first();
            array_push($brandTop ,$send);
        }

        if ($request->allAbility){
            $abilityId = [];
            for ( $i = 0; $i < count($post); $i++) {
                $abilitiesIds = json_decode($post[$i]['review'][0]['ability'],true);
                if ($abilitiesIds != null){
                    for ( $i2 = 0; $i2 < count($abilitiesIds); $i2++) {
                        for ( $i3 = 0; $i3 < count($request->allAbility); $i3++) {
                            if ($request->allAbility[$i3] == $abilitiesIds[$i2]['name']){
                                array_push($abilityId , $post[$i]['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $abilityId = Post::whereIn('id',$id3)->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allSize){
            $sizeId = [];
            for ( $i = 0; $i < count($post); $i++) {
                $sizeIds = json_decode($post[$i]['review'][0]['size'] , true);
                if ($sizeIds != null){
                    for ( $i2 = 0; $i2 < count($sizeIds); $i2++) {
                        for ( $i3 = 0; $i3 < count($request->allSize); $i3++) {
                            if ($request->allSize[$i3] == $sizeIds[$i2]->name){
                                array_push($sizeId , $post[$i]['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $sizeId = Post::whereIn('id',$id3)->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->search){
            $searchId = Post::whereIn('id',$id3)->latest()->where("title" , "LIKE" , "%{$request->search}%")->where('status' , 1)->pluck('id')->toArray();
        }else{
            $searchId = Post::whereIn('id',$id3)->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allColor){
            $colorId = [];
            for ( $i = 0; $i < count($post); $i++) {
                $colorIds = json_decode($post[$i]['review'][0]['colors'] , true);
                if ($colorIds != null){
                    for ( $i2 = 0; $i2 < count($colorIds); $i2++) {
                        for ( $i3 = 0; $i3 < count($request->allColor); $i3++) {
                            if ($request->allColor[$i3] == $colorIds[$i2]['name']){
                                array_push($colorId , $post[$i]['id']);
                            }
                        }
                    }
                }
            }
        }else{
            $colorId = Post::whereIn('id',$id3)->where('status' , 1)->pluck('id')->toArray();
        }
        if ($request->max){
            $rangeId = Post::whereIn('id',$id3)->where('price', '>=', $request->min)->where('status' , 1)->where('price', '<=', $request->max)->pluck('id')->toArray();
        }else{
            $rangeId = Post::whereIn('id',$id3)->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allOff){
            $offId = Post::whereIn('id',$id3)->whereIn('off' , $request->allOff)->where('status' , 1)->pluck('id')->toArray();
        }else{
            $offId = Post::whereIn('id',$id3)->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->suggest){
            $suggestId = Post::whereIn('id',$id3)->where('suggest' , '!=' , null)->where('status' , 1)->pluck('id')->toArray();
        }else{
            $suggestId = Post::whereIn('id',$id3)->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->count){
            $countId = Post::whereIn('id',$id3)->where('count' , '!=' , '0')->where('status' , 1)->pluck('id')->toArray();
        }else{
            $countId = Post::whereIn('id',$id3)->where('status' , 1)->pluck('id')->toArray();
        }

        if ($request->allBrands){
            $brandId1 = [];
            $brandId = [];
            for ( $i = 0; $i < count($request->allBrands); $i++) {
                $brandCheck1 = Brand::where('name', $request->allBrands[$i])->first();
                $brandCheck = $brandCheck1->post()->pluck('id');
                array_push($brandId1 , $brandCheck);
            }
            for ( $i = 0; $i < count($brandId1[0]); $i++) {
                $send = Post::where('id' , $brandId1[0][$i])->pluck('id')->first();
                array_push($brandId , $send);
            }
        }else{
            $brandId = Post::whereIn('id',$id3)->where('status' , 1)->pluck('id')->toArray();
        }

        $arrayFilter = array_intersect($brandId, $offId,$rangeId,$colorId,$searchId,$countId,$suggestId,$sizeId,$abilityId);

        $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();

        if($vidget['name'] == 'محصولات دانلودی'){
            if ($request->show == 0){
                if($vidget['type'] == 3){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->latest()->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 0){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('count' , '>=' , 1)->latest()->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 1){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('off' , '!=' , null)->latest()->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 2){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('suggest' , '!=' , null)->latest()->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
            }
            if ($request->show == 2){
                if($vidget['type'] == 3){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 0){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('count' , '>=' , 1)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 1){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('off' , '!=' , null)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 2){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('suggest' , '!=' , null)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
            }
            if ($request->show == 1 or $request->show == 3){
                if($vidget['type'] == 3){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->withCount('view')->orderBy('view_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 0){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('count' , '>=' , 1)->withCount('view')->orderBy('view_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 1){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('off' , '!=' , null)->withCount('view')->orderBy('view_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 2){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('suggest' , '!=' , null)->withCount('view')->orderBy('view_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
            }
            if ($request->show == 4){
                if($vidget['type'] == 3){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->orderBy('price')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 0){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('count' , '>=' , 1)->orderBy('price')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 1){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('off' , '!=' , null)->orderBy('price')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 2){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('suggest' , '!=' , null)->orderBy('price')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
            }
            if ($request->show == 5){
                if($vidget['type'] == 3){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->orderBy('price','DESC')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 0){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('count' , '>=' , 1)->orderBy('price','DESC')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 1){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('off' , '!=' , null)->orderBy('price','DESC')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 2){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 1)->where('variety' , 0)->where('suggest' , '!=' , null)->orderBy('price','DESC')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
            }
            if($vidget['type'] == 3){
                $showcase = Post::whereIn('id',$id3)->where('variety' , 0)->where('type' , 1)->latest()->where('showcase' , 1)->where('status' , 1)->take(5)->get();
            }
            if($vidget['type'] == 0){
                $showcase = Post::whereIn('id',$id3)->where('variety' , 0)->where('type' , 1)->latest()->where('count' , '>=' , 1)->where('showcase' , 1)->where('status' , 1)->take(5)->get();
            }
            if($vidget['type'] == 1){
                $showcase = Post::whereIn('id',$id3)->where('variety' , 0)->where('type' , 1)->latest()->where('off' , '!=' , null)->where('showcase' , 1)->where('status' , 1)->take(5)->get();
            }
            if($vidget['type'] == 2){
                $showcase = Post::whereIn('id',$id3)->where('variety' , 0)->where('type' , 1)->latest()->where('suggest' , '!=' , null)->where('showcase' , 1)->where('status' , 1)->take(5)->get();
            }
        }else{
            if ($request->show == 0){
                if($vidget['type'] == 3){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->latest()->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 0){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->latest()->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 1){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->latest()->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 2){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->latest()->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
            }
            if ($request->show == 2){
                if($vidget['type'] == 3){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 0){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 1){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 2){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->withCount('payMeta')->orderBy('pay_meta_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
            }
            if ($request->show == 1 or $request->show == 3){
                if($vidget['type'] == 3){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->withCount('view')->orderBy('view_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 0){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->withCount('view')->orderBy('view_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 1){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->withCount('view')->orderBy('view_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 2){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->withCount('view')->orderBy('view_count','DESC' )->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
            }
            if ($request->show == 4){
                if($vidget['type'] == 3){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->orderBy('price')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 0){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->orderBy('price')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 1){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->orderBy('price')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 2){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->orderBy('price')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
            }
            if ($request->show == 5){
                if($vidget['type'] == 3){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->orderBy('price','DESC')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 0){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('count' , '>=' , 1)->orderBy('price','DESC')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 1){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('off' , '!=' , null)->orderBy('price','DESC')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
                if($vidget['type'] == 2){
                    $catPost = Post::whereIn('id',$id3)->where('type' , 0)->where('variety' , 0)->where('suggest' , '!=' , null)->orderBy('price','DESC')->whereIn('id' , $arrayFilter)->where('status' , 1)->with('review')->paginate($showPostPage);
                }
            }
            if($vidget['type'] == 3){
                $showcase = Post::whereIn('id',$id3)->where('variety' , 0)->where('type' , 0)->latest()->where('showcase' , 1)->where('status' , 1)->take(5)->get();
            }
            if($vidget['type'] == 0){
                $showcase = Post::whereIn('id',$id3)->where('variety' , 0)->where('type' , 0)->latest()->where('count' , '>=' , 1)->where('showcase' , 1)->where('status' , 1)->take(5)->get();
            }
            if($vidget['type'] == 1){
                $showcase = Post::whereIn('id',$id3)->where('variety' , 0)->where('type' , 0)->latest()->where('off' , '!=' , null)->where('showcase' , 1)->where('status' , 1)->take(5)->get();
            }
            if($vidget['type'] == 2){
                $showcase = Post::whereIn('id',$id3)->where('variety' , 0)->where('type' , 0)->latest()->where('suggest' , '!=' , null)->where('showcase' , 1)->where('status' , 1)->take(5)->get();
            }
        }
        return Inertia::render('Archive/ProductArchive' , [
            'cats' => $cats,
            'showcase' => $showcase,
            'catPost' => $catPost,
            'title' => $title,
            'minPrice' => $minPrice,
            'catsTop' => $catsTop,
            'brandTop' => $brandTop,
            'url' => $url,
            'maxPrice' => $maxPrice,
            'off' => $off,
            'size' => $size,
            'ability' => $ability,
            'brands' => $brands,
            'color' => $color,
        ]);
    }

    public function news(){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first() ?:'' ;
        $shortActivity = Setting::where('key' , 'descriptionSeo')->pluck('value')->first() ?:'' ;
        $this->seoSingleSeo(   'اخبار' . " - $title " , $shortActivity , 'store' , 'news' , $logo );
        $url = request()->path();
        $titleSite = Setting::where('key' , 'siteTitle')->pluck('value')->first();
        $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();
        $accept = News::where('status' , 1)->where('accept' , 1)->with('user','category')->latest()->take(10)->get();
        $suggest = News::where('status' , 1)->where('suggest' , 1)->with('user')->latest()->take(6)->get();
        $news = News::where('status' , 1)->latest()->with('user')->paginate($showPostPage);
        return Inertia::render('Archive/AllNews' , [
            'news' => $news,
            'url' => $url,
            'accept' => $accept,
            'titleSite' => $titleSite,
            'suggest' => $suggest,
        ]);
    }

    public function newsCategory(Category $category){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first() ?:'' ;
        $shortActivity = Setting::where('key' , 'descriptionSeo')->pluck('value')->first() ?:'' ;
        $this->seoSingleSeo(   $category->name . " - $title " , $shortActivity , 'store' , 'news/archive/category/'."$category->slug" , $logo );

        $url = request()->path();
        $titleSite = Setting::where('key' , 'siteTitle')->pluck('value')->first();
        $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();
        $accept = $category->news()->where('status' , 1)->where('accept' , 1)->with('user','category')->latest()->take(10)->get();
        $suggest = $category->news()->where('status' , 1)->where('suggest' , 1)->with('user')->latest()->take(6)->get();
        $news = $category->news()->where('status' , 1)->latest()->with('user')->paginate($showPostPage);
        return Inertia::render('Archive/AllNews' , [
            'news' => $news,
            'url' => $url,
            'titleSite' => $titleSite,
            'accept' => $accept,
            'suggest' => $suggest,
        ]);
    }

    public function newsTag(Tag $tag){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first() ?:'' ;
        $shortActivity = Setting::where('key' , 'descriptionSeo')->pluck('value')->first() ?:'' ;
        $this->seoSingleSeo(   $tag->name . " - $title " , $shortActivity , 'store' , 'news/archive/tag/'."$tag->slug" , $logo );

        $url = request()->path();
        $titleSite = Setting::where('key' , 'siteTitle')->pluck('value')->first();
        $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();
        $accept = $tag->news()->where('status' , 1)->where('accept' , 1)->with('user','category')->latest()->take(10)->get();
        $suggest = $tag->news()->where('status' , 1)->where('suggest' , 1)->with('user')->latest()->take(6)->get();
        $news = $tag->news()->where('status' , 1)->latest()->with('user')->paginate($showPostPage);
        return Inertia::render('Archive/AllNews' , [
            'news' => $news,
            'accept' => $accept,
            'url' => $url,
            'titleSite' => $titleSite,
            'suggest' => $suggest,
        ]);
    }

    public function searchNav(Request $request){
        $product = Post::where("title" , "LIKE" , "%{$request->search}%")->where('variety' , 0)->where('status' , 1)->take(10)->get();
        if(count($product) == 0){
            $product = Post::where("product_id" , "LIKE" , "%{$request->search}%")->where('variety' , 0)->where('status' , 1)->take(10)->get();
        }
        return $product;
    }

    public function rank(Request $request){
        if(auth()->user()){
            $title = Setting::where('key' , 'title')->pluck('value')->first();
            $myScore = Score::latest()->where('type' , 0)->where('user_id' , auth()->user()->id)->pluck('name')->sum();
            $myRank = Rank::where('from' , '<=', $myScore)->where('to' , '>=', $myScore)->first();
            if($myRank){
                $posts = $myRank->post()->where('status' , 1)->where('type' , 0)->where('variety' , 0)->get();
                return Inertia::render('Archive/SuggestProduct', [
                    'title' => $title,
                    'posts' => $posts,
                    'rank' => $myRank,
                ]);
            }else{
                return abort(404);
            }
        }else{
            return abort(404);
        }
    }
}
