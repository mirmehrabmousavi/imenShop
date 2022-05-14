<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Charge;
use App\Models\Page;
use App\Models\Rank;
use App\Models\Score;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\Pay;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Inertia::share('appName', config('app.name'));

// Lazily
        Inertia::share('errors', function(){
            return session()->get('errors') ? session()->get('errors')->getBag('default')->getMessages() : (object) [];
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot()
    {
        $cookie1 = Cookie::has('theme');
        $this->cookie1 = $cookie1;
        Inertia::share('appName', config('app.name'));

        $catHeader1 = Setting::where('key' , 'catHeader')->pluck('value')->first();
        $catHeader = [];
        if ($catHeader1 != null){
            $allCatHeader1 = explode('[' , $catHeader1);
            $allCatHeader2 = explode(']' , $allCatHeader1[1]);
            $allCatHeader3 = explode(',' , $allCatHeader2[0]);
            foreach ($allCatHeader3 as $item){
                $send = Category::where('id' , $item)->with(["cats" => function($q){
                    $q->latest()->with(["cats" => function($q){
                        $q->latest()->with(["cats" => function($q){
                            $q->latest()->with('cats');}]);}]);}])->first();
                if($send){
                    array_push($catHeader ,$send);
                }
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
                if($send){
                    array_push($catFooter ,$send);
                }
            };
        }

        Inertia::share('allow', fn (Request $request) => $request->user()
            ? $request->user()->getAllPermissions()
            : null
        );
        Inertia::share('userData', fn (Request $request) => $request->user()
            ? $request->user()
            : null
        );
        Inertia::share('wallet', fn (Request $request) => $request->user()
            ? Charge::latest()->where('type' , 0)->where('user_id' , auth()->user()->id)->where('status' , 100)->pluck('price')->sum() -Charge::latest()->where('type' , 1)->where('user_id' , auth()->user()->id)->where('status' , 100)->pluck('price')->sum()
            : null
        );
        Inertia::share('myScore', fn (Request $request) => $request->user()
            ? Score::latest()->where('type' , 0)->where('user_id' , auth()->user()->id)->pluck('name')->sum() -Score::latest()->where('type' , 1)->where('user_id' , auth()->user()->id)->pluck('name')->sum()
            : null
        );
        Inertia::share('ticketSeen', fn (Request $request) => Ticket::where('seen' , 0)->get()
            ? Ticket::where('seen' , 0)->count()
            : null
        );
        Inertia::share('userSeen', fn (Request $request) => User::where('seen' , 0)->get()
            ? User::where('seen' , 0)->count()
            : null
        );
        Inertia::share('commentSeen', fn (Request $request) => Comment::where('seen' , 0)->get()
            ? Comment::where('seen' , 0)->count()
            : null
        );
        Inertia::share('pageHome', fn (Request $request) => Page::get()
            ? Page::get()
            : []
        );
        Inertia::share('paySeen', fn (Request $request) => Pay::where('seen' , 0)->get()
            ? count(Pay::where('seen' , 0)->get())
            : null
        );
        Inertia::share('logo', fn (Request $request) => Setting::where('key' , 'logo')->pluck('value')->first()
            ? Setting::where('key' , 'logo')->pluck('value')->first()
            : null
        );
        Inertia::share('siteTheme', fn (Request $request) => Setting::where('key' , 'theme')->pluck('value')->first()
            ? Setting::where('key' , 'theme')->pluck('value')->first()
            : null
        );
        Inertia::share('dark', fn (Request $request) => Setting::where('key' , 'dark')->pluck('value')->first()
            ? Setting::where('key' , 'dark')->pluck('value')->first()
            : null
        );
        Inertia::share('siteLanguages', fn (Request $request) => Setting::where('key' , 'languages')->pluck('value')->first()
            ? Setting::where('key' , 'languages')->pluck('value')->first()
            : null
        );
        Inertia::share('emailAddress', fn (Request $request) => Setting::where('key' , 'email')->pluck('value')->first()
            ? Setting::where('key' , 'email')->pluck('value')->first()
            : null
        );
        Inertia::share('numberSite', fn (Request $request) => Setting::where('key' , 'number')->pluck('value')->first()
            ? Setting::where('key' , 'number')->pluck('value')->first()
            : null
        );
        Inertia::share('telegramSite', fn (Request $request) => Setting::where('key' , 'telegram')->pluck('value')->first()
            ? Setting::where('key' , 'telegram')->pluck('value')->first()
            : null
        );
        Inertia::share('twitterSite', fn (Request $request) => Setting::where('key' , 'twitter')->pluck('value')->first()
            ? Setting::where('key' , 'twitter')->pluck('value')->first()
            : null
        );
        Inertia::share('instagramSite', fn (Request $request) => Setting::where('key' , 'instagram')->pluck('value')->first()
            ? Setting::where('key' , 'instagram')->pluck('value')->first()
            : null
        );
        Inertia::share('etemadAddress', fn (Request $request) => Setting::where('key' , 'etemad')->pluck('value')->first()
            ? Setting::where('key' , 'etemad')->pluck('value')->first()
            : null
        );
        Inertia::share('fanavariAddress', fn (Request $request) => Setting::where('key' , 'fanavari')->pluck('value')->first()
            ? Setting::where('key' , 'fanavari')->pluck('value')->first()
            : null
        );
        Inertia::share('facebookSite', fn (Request $request) => Setting::where('key' , 'facebook')->pluck('value')->first()
            ? Setting::where('key' , 'facebook')->pluck('value')->first()
            : null
        );
        Inertia::share('addressSite', fn (Request $request) => Setting::where('key' , 'address')->pluck('value')->first()
            ? Setting::where('key' , 'address')->pluck('value')->first()
            : null
        );
        Inertia::share('aboutFooter', fn (Request $request) => Setting::where('key' , 'about')->pluck('value')->first()
            ? Setting::where('key' , 'about')->pluck('value')->first()
            : null
        );
        Inertia::share('aboutFooterEn', fn (Request $request) => Setting::where('key' , 'aboutEn')->pluck('value')->first()
            ? Setting::where('key' , 'aboutEn')->pluck('value')->first()
            : null
        );
        Inertia::share('catList', fn (Request $request) => $catHeader
            ?$catHeader

            : []
        );
        Inertia::share('catFooter', fn (Request $request) => $catFooter
            ?$catFooter

            : []
        );
        Inertia::share('showSearch', fn (Request $request) => Setting::where('key' , 'search')->pluck('value')->first()
            ? Setting::where('key' , 'search')->pluck('value')->first()
            : null
        );
        Inertia::share('checkSeller', fn (Request $request) => auth()->user()
            ? auth()->user()->getAllPermissions()->where('name' , 'فروشنده')->pluck('name')->first()
            : null
        );
        Inertia::share('myRank', function (Request $request){
            if(auth()->user()){
                $myScore = Score::latest()->where('type' , 0)->where('user_id' , auth()->user()->id)->pluck('name')->sum();
                $myRank = Rank::where('from' , '<=', $myScore)->where('to' , '>=', $myScore)->first();
            }else{
                $myRank = '';
            }
            return $myRank;
        });
        view()->composer('app', function ($view) {
            $url = Route::current()->getName();
            $dark = Setting::where('key' , 'dark')->pluck('value')->first();
            if($this->cookie1 == '1'){
                $theme = $_COOKIE['theme'];
            }else{
                $theme = $dark;
            }
            $view->with(['url'=>$url,'theme'=>$theme]);
        });
    }
}
