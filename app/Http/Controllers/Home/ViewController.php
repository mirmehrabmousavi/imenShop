<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\View;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class ViewController extends Controller
{
    public function view(Request $request){
        $check = 'no';
        $agent = new Agent();
        $platform = $agent->platform();
        $browser = $agent->browser();;
        $user_ip = $request->ip();
        $views = View::where('user_ip' , $user_ip)->whereDate('created_at' , Carbon::today())->get();
        for ( $i = 0; $i < count($views); $i++) {
            $views1 = $views[$i]->post()->where('id' , $request->postId)->first();
            if ($views1){
                $check = 'yes';
            }
        }
        if ($check == 'no'){
            $view = View::create([
                'browser'=>$browser,
                'platform'=>$platform,
                'user_ip'=>$user_ip,
            ]);
            $view->post()->sync($request->postId);
            if (auth()->user()){
                $view->user()->sync(auth()->user()->id);
            }
        }
    }
}
