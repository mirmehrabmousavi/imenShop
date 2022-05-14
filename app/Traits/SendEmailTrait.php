<?php


namespace App\Traits;


use App\Mail\SendMail;
use App\Models\Setting;
use Ghasedak\GhasedakApi;
use Illuminate\Support\Facades\Mail;

trait SendEmailTrait
{
    public function sendEmail($email,$message,$title){
        $allEmail = Setting::where('key' , 'email')->pluck('value')->first();
        Mail::to($email)->send(new sendMail($title , $message , $allEmail));
    }
}
