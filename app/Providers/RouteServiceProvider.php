<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\News;
use App\Models\Page;
use App\Models\Pay;
use App\Models\Post;
use App\Models\Robot;
use App\Models\Tag;
use App\Models\Vidget;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/profile';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        Route::bind('PostSlug', function ($value) {
            return Post::where('slug', $value)->where('type', 0)->where('status', 1)->firstOrFail();
        });
        Route::bind('DownloadSlug', function ($value) {
            return Post::where('slug', $value)->where('type', 1)->where('status', 1)->firstOrFail();
        });
        Route::bind('NewsSlug', function ($value) {
            return News::where('slug', $value)->where('status', 1)->firstOrFail();
        });
        Route::bind('CategorySlug', function ($value) {
            return Category::where('slug', $value)->where('type' , 0)->firstOrFail();
        });
        Route::bind('CategoryNewsSlug', function ($value) {
            return Category::where('slug', $value)->where('type' , 1)->firstOrFail();
        });
        Route::bind('MyPostSlug', function ($value) {
            return Post::where('slug', $value)->where('variety' , 0)->where('user_id', auth()->user()->id)->firstOrFail();
        });
        Route::bind('TagSlug', function ($value) {
            return Tag::where('slug', $value)->firstOrFail();
        });
        Route::bind('PayId', function ($value) {
            return Pay::where('property', $value)->firstOrFail();
        });
        Route::bind('BrandSlug', function ($value) {
            return Brand::where('slug', $value)->firstOrFail();
        });
        Route::bind('VidgetSlug', function ($value) {
            return Vidget::where('slug', $value)->firstOrFail();
        });
        Route::bind('PageSlug', function ($value) {
            return Page::where('slug', $value)->firstOrFail();
        });
        Route::bind('RobotId', function ($value) {
            return Robot::where('id', $value)->where('status' , 1)->firstOrFail();
        });
        Route::bind('PayProperty', function ($value) {
            if (auth()->user()){
                return Pay::where('property', $value)->where('user_id' , auth()->user()->id)->firstOrFail();
            }else{
                return abort(404);
            }
        });

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60);
        });
    }
}
