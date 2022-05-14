<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class SettingController extends Controller
{
    public function comment(){
        $pic = Setting::where('key' , 'userPic')->pluck('value')->first();
        $forbidden = Setting::where('key' , 'forbiddens')->pluck('value')->first();
        $show = Setting::where('key' , 'showUser')->pluck('value')->first();
        $limit = Setting::where('key' , 'limited')->pluck('value')->first();
        $page = Setting::where('key' , 'pages')->pluck('value')->first();
        $approve = Setting::where('key' , 'approved')->pluck('value')->first();
        $replay = Setting::where('key' , 'reply')->pluck('value')->first();
        $coercions = Setting::where('key' , 'coercion')->pluck('value')->first();
        $check = Setting::where('key' , 'checkUser')->pluck('value')->first();
        $online = Setting::where('key' , 'checkOnline')->pluck('value')->first();
        Inertia::setRootView('admin');
        return Inertia::render('CommentSetting' , [
            'pic' => $pic,
            'forbidden' => $forbidden,
            'show' => $show,
            'limit' => $limit,
            'page' => $page,
            'approve' => $approve,
            'replay' => $replay,
            'coercions' => $coercions,
            'check' => $check,
            'online' => $online,
        ]);
    }

    public function storeComment(Request $request)
    {
        $userPic = $request->userPic;
        $forbiddens = $request->forbiddens;
        $showUser = $request->showUser;
        $limited = $request->limited;
        $pages = $request->pages;
        $approved = $request->approved;
        $reply = $request->reply;
        $coercion = $request->coercion;
        $checkUser = $request->checkUser;
        $checkOnline = $request->checkOnline;
        $array = [
            'userPic' =>$userPic,
            'forbiddens' =>$forbiddens,
            'showUser' =>$showUser,
            'limited' =>$limited,
            'pages' =>$pages,
            'approved' =>$approved,
            'reply' =>$reply,
            'coercion' =>$coercion,
            'checkUser' =>$checkUser,
            'checkOnline' =>$checkOnline,
        ];
        foreach ($array as $key=>$item){
            $setting = Setting::where('key' , $key)->first();
            if ($setting != ''){
                $setting->update([
                    'value'=>$item,
                ]);
            }
        }
        return redirect('/admin/setting/setting-comment');
    }

    public function manage(){
        $logo = Setting::where('key' , 'logo')->pluck('value')->first();
        $map = Setting::where('key' , 'map')->pluck('value')->first();
        $about = Setting::where('key' , 'about')->pluck('value')->first();
        $aboutEn = Setting::where('key' , 'aboutEn')->pluck('value')->first();
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $email = Setting::where('key' , 'email')->pluck('value')->first();
        $role = Setting::where('key' , 'role')->pluck('value')->first();
        $fanavari = Setting::where('key' , 'fanavari')->pluck('value')->first();
        $languages = Setting::where('key' , 'languages')->pluck('value')->first();
        $etemad = Setting::where('key' , 'etemad')->pluck('value')->first();
        $number = Setting::where('key' , 'number')->pluck('value')->first();
        $facebook = Setting::where('key' , 'facebook')->pluck('value')->first();
        $instagram = Setting::where('key' , 'instagram')->pluck('value')->first();
        $twitter = Setting::where('key' , 'twitter')->pluck('value')->first();
        $telegram = Setting::where('key' , 'telegram')->pluck('value')->first();
        $verify = Setting::where('key' , 'verify')->pluck('value')->first();
        $showPostCategory = Setting::where('key' , 'showPostCategory')->pluck('value')->first();
        $productId = Setting::where('key' , 'productId')->pluck('value')->first();
        $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();
        $tokenApp = Setting::where('key' , 'tokenApp')->pluck('value')->first();
        $name = Setting::where('key' , 'name')->pluck('value')->first();
        $sms = Setting::where('key' , 'sms')->pluck('value')->first();
        $dark = Setting::where('key' , 'dark')->pluck('value')->first();
        $theme = Setting::where('key' , 'theme')->pluck('value')->first();
        $roles = Role::latest()->get();
        Inertia::setRootView('admin');
        return Inertia::render('SettingManage' , [
            'tokenApp' => $tokenApp,
            'fanavari' => $fanavari,
            'productId' => $productId,
            'name' => $name,
            'sms' => $sms,
            'dark' => $dark,
            'theme' => $theme,
            'languages' => $languages,
            'etemad' => $etemad,
            'number' => $number,
            'facebook' => $facebook,
            'instagram' => $instagram,
            'twitter' => $twitter,
            'telegram' => $telegram,
            'roles' => $roles,
            'logo' => $logo,
            'map' => $map,
            'about' => $about,
            'aboutEn' => $aboutEn,
            'title' => $title,
            'address' => $address,
            'email' => $email,
            'role' => $role,
            'verify' => $verify,
            'showPostCategory' => $showPostCategory,
            'showPostPage' => $showPostPage,
        ]);
    }

    public function storeManage(Request $request){
        $showPostCategory = $request->showPostCategory;
        $showPostPage = $request->showPostPage;
        $fanavari = $request->fanavari;
        $etemad = $request->etemad;
        $number = $request->number;
        $sms = $request->sms;
        $map = $request->map;
        $facebook = $request->facebook;
        $instagram = $request->instagram;
        $twitter = $request->twitter;
        $telegram = $request->telegram;
        $logo = $request->image;
        $about = $request->about;
        $aboutEn = $request->aboutEn;
        $title = $request->title;
        $address = $request->address;
        $role = $request->role;
        $verify = $request->verify;
        $productId = $request->productId;
        $name = $request->name;
        $languages = $request->language;
        $email = $request->email;
        $dark = $request->dark;
        $theme = $request->theme;
        $tokenApp = $request->tokenApp;
        $array = [
            'tokenApp' => $tokenApp,
            'showPostCategory' =>$showPostCategory,
            'showPostPage' =>$showPostPage,
            'productId' =>$productId,
            'name' =>$name,
            'sms' =>$sms,
            'dark' =>$dark,
            'theme' =>$theme,
            'map' =>$map,
            'fanavari' =>$fanavari,
            'telegram' =>$telegram,
            'languages' => $languages,
            'etemad' =>$etemad,
            'twitter' =>$twitter,
            'instagram' =>$instagram,
            'facebook' =>$facebook,
            'number' =>$number,
            'logo' =>$logo,
            'about' =>$about,
            'aboutEn' =>$aboutEn,
            'title' =>$title,
            'address' =>$address,
            'role' =>$role,
            'email' =>$email,
            'verify' =>$verify,
        ];
        foreach ($array as $key=>$item){
            $setting = Setting::where('key' , $key)->first();
            if ($setting != ''){
                $setting->update([
                    'value'=>$item,
                ]);
            }
        }
        return redirect('/admin/setting/setting-manage');
    }

    public function seo(){
        $titleSeo = Setting::where('key' , 'titleSeo')->pluck('value')->first();
        $keywords = Setting::where('key' , 'keywords')->pluck('value')->first();
        $descriptionSeo = Setting::where('key' , 'descriptionSeo')->pluck('value')->first();
        Inertia::setRootView('admin');
        return Inertia::render('SeoSetting' , [
            'titleSeo' => $titleSeo,
            'keywords' => $keywords,
            'descriptionSeo' => $descriptionSeo,
        ]);
    }

    public function settingCategory(){
        $categories = Category::latest()->get();
        $catHeader1 = Setting::where('key' , 'catHeader')->pluck('value')->first();
        $catHeader = [];
        if ($catHeader1 != null){
            $allCatHeader1 = explode('[' , $catHeader1);
            $allCatHeader2 = explode(']' , $allCatHeader1[1]);
            $allCatHeader3 = explode(',' , $allCatHeader2[0]);
            foreach ($allCatHeader3 as $item){
                $send = Category::where('id' , $item)->first();
                array_push($catHeader ,$send);
            }
        }

        $catFooter1 = Setting::where('key' , 'catFooter')->pluck('value')->first();
        $catFooter = [];
        if ($catFooter1 != null){
            $allCatFooter1 = explode('[' , $catFooter1);
            $allCatFooter2 = explode(']' , $allCatFooter1[1]);
            $allCatFooter3 = explode(',' , $allCatFooter2[0]);
            foreach ($allCatFooter3 as $item){
                $send = Category::where('id' , $item)->first();
                array_push($catFooter ,$send);
            };
        }
        Inertia::setRootView('admin');
        return Inertia::render('CategorySetting' , [
            'categories' => $categories,
            'catFooter' => $catFooter,
            'catHeader' => $catHeader,
        ]);
    }

    public function storeCategory(Request $request){
        $catHeader1 = $request->categoryHeader;
        $catHeader = [];
        for ( $i = 0; $i < count($catHeader1); $i++) {
            if ($catHeader1[$i]){
                $send = $catHeader1[$i]['id'];
                array_push($catHeader , $send);
            }
        }

        $categoryFooter1 = $request->categoryFooter;
        $categoryFooter = [];
        for ( $i = 0; $i < count($categoryFooter1); $i++) {
            if ($categoryFooter1[$i]){
                $send = $categoryFooter1[$i]['id'];
                array_push($categoryFooter , $send);
            }
        }

        $array = [
            'catHeader' =>$catHeader,
            'catFooter' =>$categoryFooter,
        ];
        foreach ($array as $key=>$item){
            $setting = Setting::where('key' , $key)->first();
            if ($setting != ''){
                $setting->update([
                    'value'=>$item,
                ]);
            }
        }
        return redirect('/admin/setting/setting-category');
    }

    public function storeSeo(Request $request){
        $titleSeo = $request->titleSeo;
        $keywords = $request->keywords;
        $descriptionSeo = $request->descriptionSeo;
        $array = [
            'descriptionSeo' =>$descriptionSeo,
            'keywords' =>$keywords,
            'titleSeo' =>$titleSeo,
        ];
        foreach ($array as $key=>$item){
            $setting = Setting::where('key' , $key)->first();
            if ($setting != ''){
                $setting->update([
                    'value'=>$item,
                ]);
            }
        }
        return redirect('/admin/setting/seo');
    }

    public function pay(Request $request){
        if($request->update){
            $choicePay = $request->choicePay;
            $choicePayApp = $request->choicePayApp;
            $zibal = $request->zibal;
            $idpay = $request->idpay;
            $zarinpal = $request->zarinpal;
            $nextPay = $request->nextPay;
            $array = [
                'nextPay' =>$nextPay,
                'zarinpal' =>$zarinpal,
                'idpay' =>$idpay,
                'zibal' =>$zibal,
                'choicePay' =>$choicePay,
                'choicePayApp' =>$choicePayApp,
            ];
            foreach ($array as $key=>$item){
                $setting = Setting::where('key' , $key)->first();
                if ($setting != ''){
                    $setting->update([
                        'value'=>$item,
                    ]);
                }
            }
        }
        $choicePay = Setting::where('key' , 'choicePay')->pluck('value')->first();
        $choicePayApp = Setting::where('key' , 'choicePayApp')->pluck('value')->first();
        $zibal = Setting::where('key' , 'zibal')->pluck('value')->first();
        $idpay = Setting::where('key' , 'idpay')->pluck('value')->first();
        $zarinpal = Setting::where('key' , 'zarinpal')->pluck('value')->first();
        $nextPay = Setting::where('key' , 'nextPay')->pluck('value')->first();
        Inertia::setRootView('admin');
        return Inertia::render('PaySetting' , [
            'choicePayApp' => $choicePayApp,
            'choicePay' => $choicePay,
            'idpay' => $idpay,
            'zibal' => $zibal,
            'zarinpal' => $zarinpal,
            'nextPay' => $nextPay,
        ]);
    }
}
