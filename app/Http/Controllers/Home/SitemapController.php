<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index(){
        $sitemap = app()->make('sitemap');
        $sitemap->setCache('laravel.sitemap', 60);
        if (!$sitemap->isCached()){
            $sitemap->add(url()->to('/sitemap-categories') , '2021-01-01T20:10:00+02:00','0.8','monthly');
            $sitemap->add(url()->to('/sitemap-products') , '2021-01-01T20:10:00+02:00','0.8','monthly');
            $sitemap->add(url()->to('/sitemap-tags') , '2021-01-01T20:10:00+02:00','0.8','monthly');
        }
        return $sitemap->render('xml');
    }
    public function tags(){
        $sitemap = app()->make('sitemap');
        $sitemap->setCache('laravel.sitemap.tags', 60);

        if (!$sitemap->isCached()){
            $tags = Tag::latest()->take(50)->get();
            foreach ($tags as $tag){
                $sitemap->add(url()->to('/news/archive/tag/'. $tag->slug) ,$tag->updated_at,'0.8','monthly');
            }
        }
        return $sitemap->render('xml');
    }
    public function brands(){
        $sitemap = app()->make('sitemap');
        $sitemap->setCache('laravel.sitemap.tags', 60);

        if (!$sitemap->isCached()){
            $brands = Brand::latest()->take(50)->get();
            foreach ($brands as $brand){
                $sitemap->add(url()->to('/archive/brand/'. $brand->slug) ,$brand->updated_at,'0.8','monthly');
            }
        }
        return $sitemap->render('xml');
    }
    public function categories(){
        $sitemap = app()->make('sitemap');
        $sitemap->setCache('laravel.sitemap.categories', 60);

        if (!$sitemap->isCached()){
            $categories = Category::latest()->take(50)->get();
            foreach ($categories as $category){
                $sitemap->add(url()->to('/archive/category/'. $category->slug) ,$category->updated_at,'0.8','monthly');
            }
        }
        return $sitemap->render('xml');
    }
    public function products(){
        $sitemap = app()->make('sitemap');
        $sitemap->setCache('laravel.sitemap.products', 60);

        if (!$sitemap->isCached()){
            $articles = Post::where('status' , 1)->where('type' , 0)->latest()->take(50)->get();
            foreach ($articles as $article){
                $sitemap->add(url()->to('/product/'.$article->slug) ,$article->updated_at,'0.8','monthly');

            }
        }
        return $sitemap->render('xml');
    }
    public function downloadable(){
        $sitemap = app()->make('sitemap');
        $sitemap->setCache('laravel.sitemap.downloadable', 60);

        if (!$sitemap->isCached()){
            $articles = Post::where('status' , 1)->where('type' , 1)->latest()->take(50)->get();
            foreach ($articles as $article){
                $sitemap->add(url()->to('/download-product/'.$article->slug) ,$article->updated_at,'0.8','monthly');

            }
        }
        return $sitemap->render('xml');
    }
}
