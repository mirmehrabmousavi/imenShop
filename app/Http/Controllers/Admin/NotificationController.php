<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\Setting;
use App\Traits\SendEmailTrait;
use App\Traits\SendSmsTrait;
use Illuminate\Http\Request;
use Melipayamak\MelipayamakApi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use nusoap_client;

class NotificationController extends Controller
{
    use SendSmsTrait;
    use SendEmailTrait;
    public function email(Request $request){
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش اطلاع رسانی خودش')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف اطلاع رسانی')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن اطلاع رسانی')->pluck('name');
        if(auth()->user()->admin == 1 or count($checkDeletes) >= 1){
            $deletes = 1;
        }else{
            $deletes = 0;
        }
        if(auth()->user()->admin == 1 or count($checkAdds) >= 1){
            $adds = 1;
        }else{
            $adds = 0;
        }
        if(auth()->user()->admin == 1 or count($showSome) >= 1){
            $shows = 1;
        }else{
            $shows = 0;
        }
        if($request->value){
            foreach ($request->value as $value) {
                $tax = Notification::where('id', $value)->first();
                $tax->user()->detach();
            }
            DB::table('notifications')->whereIn('id', $request->value)->delete();
        }
        if($request->notify_id){
            $notifyShow = Notification::where('id' , $request->notify_id)->first();
        }else{
            $notifyShow = '';
        }
        if($request->title || $request->body){
            $request->validate([
                'title' => 'required|max:255',
                'body' => 'required',
            ]);
            foreach($request->users as $item){
                $email = User::where('id' , $item)->pluck('email')->first();
                $this->sendEmail($email , $request->body , $request->title);
            }
            Notification::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'body' => $request->body,
                'type' => 0,
            ]);
        }

        if ($request->search){
            if (count($showSome) >= 1){
                $search = Notification::where('user_id' , auth()->user()->id)->where("title" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = Notification::where('user_id' , auth()->user()->id)->where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }else{
                $search = Notification::where("title" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = Notification::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }
        }else{
            if(count($showSome) >= 1){
                $search = Notification::where('user_id' , auth()->user()->id)->pluck('id')->toArray();
            }else{
                $search = Notification::latest()->pluck('id')->toArray();
            }
        }
        if ($request->date){
            if (count($showSome) >= 1){
                $date = Notification::where('user_id' , auth()->user()->id)->whereDate('created_at',$request->date)->pluck('id')->toArray();
            }else{
                $date = Notification::whereDate('created_at',$request->date)->pluck('id')->toArray();
            }
        }else{
            if(count($showSome) >= 1){
                $date = Notification::where('user_id' , auth()->user()->id)->pluck('id')->toArray();
            }else{
                $date = Notification::latest()->pluck('id')->toArray();
            }
        }
        $arrayFilter = array_intersect($search,$date);
        $taxes = Notification::latest()->whereIn('id' , $arrayFilter)->where('type' , 0)->paginate(30);
        $name='ایمیل';
        $routeAddress='email';
        $labels = ['#','آیدی','عنوان','توضیحات','تاریخ ثبت','عملیات'];
        $users = User:: where('email' , '!=' , null)->latest()->pluck('name','id');
        Inertia::setRootView('admin');
        return Inertia::render('NotificationPanel' , [
            'name' => $name,
            'users' => $users,
            'taxes' => $taxes,
            'notifyShow' => $notifyShow,
            'labels' => $labels,
            'adds' => $adds,
            'deletes' => $deletes,
            'shows' => $shows,
            'routeAddress' => $routeAddress,
        ]);
    }
    public function sms(Request $request){
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش اطلاع رسانی خودش')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف اطلاع رسانی')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن اطلاع رسانی')->pluck('name');
        if(auth()->user()->admin == 1 or count($checkDeletes) >= 1){
            $deletes = 1;
        }else{
            $deletes = 0;
        }
        if(auth()->user()->admin == 1 or count($checkAdds) >= 1){
            $adds = 1;
        }else{
            $adds = 0;
        }
        if(auth()->user()->admin == 1 or count($showSome) >= 1){
            $shows = 1;
        }else{
            $shows = 0;
        }
        if($request->value){
            foreach ($request->value as $value) {
                $tax = Notification::where('id', $value)->first();
                $tax->user()->detach();
            }
            DB::table('notifications')->whereIn('id', $request->value)->delete();
        }
        if($request->notify_id){
            $notifyShow = Notification::where('id' , $request->notify_id)->first();
        }else{
            $notifyShow = '';
        }
        if($request->title || $request->body){
            $request->validate([
                'body' => 'required|max:255',
            ]);
            foreach($request->users as $item){
                $number = User::where('id' , $item)->pluck('number')->first();
                $sms = Setting::where('key' , 'sms')->pluck('value')->first();
                if($sms == 1){
                    $username = 'behnamf59';
                    $password = 'g856bm';
                    $api = new MelipayamakApi($username,$password);
                    $sms = $api->sms();
                    $to = $number;
                    $from = '30008666637324';
                    $text = $request->body;
                    $response = $sms->send($to,$from,$text);
                }else{
                    $this->sendSms($number , $request->body,env('GHASEDAKAPI_Number'));
                }
            }
            Notification::create([
                'user_id' => Auth::id(),
                'body' => $request->body,
                'type' => 1,
            ]);
        }

        if ($request->search){
            if (count($showSome) >= 1){
                $search = Notification::where('user_id' , auth()->user()->id)->where("title" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = Notification::where('user_id' , auth()->user()->id)->where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }else{
                $search = Notification::where("title" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = Notification::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }
        }else{
            if(count($showSome) >= 1){
                $search = Notification::where('user_id' , auth()->user()->id)->pluck('id')->toArray();
            }else{
                $search = Notification::latest()->pluck('id')->toArray();
            }
        }
        if ($request->date){
            if (count($showSome) >= 1){
                $date = Notification::where('user_id' , auth()->user()->id)->whereDate('created_at',$request->date)->pluck('id')->toArray();
            }else{
                $date = Notification::whereDate('created_at',$request->date)->pluck('id')->toArray();
            }
        }else{
            if(count($showSome) >= 1){
                $date = Notification::where('user_id' , auth()->user()->id)->pluck('id')->toArray();
            }else{
                $date = Notification::latest()->pluck('id')->toArray();
            }
        }
        $arrayFilter = array_intersect($search,$date);
        $taxes = Notification::latest()->whereIn('id' , $arrayFilter)->where('type' , 1)->paginate(30);
        $name='پیامک';
        $routeAddress='sms';
        $labels = ['#','آیدی','توضیحات','تاریخ ثبت','عملیات'];
        $users = User:: where('number' , '!=' , null)->latest()->pluck('name','id');
        Inertia::setRootView('admin');
        return Inertia::render('NotificationPanel' , [
            'name' => $name,
            'users' => $users,
            'taxes' => $taxes,
            'notifyShow' => $notifyShow,
            'labels' => $labels,
            'adds' => $adds,
            'deletes' => $deletes,
            'shows' => $shows,
            'routeAddress' => $routeAddress,
        ]);
    }
}
