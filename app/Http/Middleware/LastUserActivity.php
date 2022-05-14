<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LastUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(1);
            Cache::put('user-is-online-' . Auth::user()->id, true, $expiresAt);
            $cart = auth()->user()->cart;
            if (count($cart) >= 1){
                if ($cart[0]['delivery']){
                    $carts = json_decode($cart[0]['delivery'],true)['timestamp'];
                    $v = Carbon::now();
                    $v1 = $v->timestamp;
                    if ($v1>= $carts) {
                        DB::table('carts')->whereIn('id', auth()->user()->cart()->pluck('id'))->update(['delivery'=> null]);
                    }
                }
            }
        }
        return $next($request);
    }
}
