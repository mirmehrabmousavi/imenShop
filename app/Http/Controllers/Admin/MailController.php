<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\sendMail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function email(Request $request){
        $email = Setting::where('key' , 'email')->pluck('value')->first();
        foreach ($request->user as $user) {
            Mail::to($user)->send(new sendMail($request->title , $request->message , $email));
        }
        return redirect('/admin/ticket');
    }
}
