<?php


namespace App\Traits;


use Ghasedak\GhasedakApi;

trait SendSmsTrait
{
    public function sendSms($number,$message,$line){
        $api = new GhasedakApi(env('GHASEDAKAPI_KEY'));
        $api->SendSimple(
            "$number",  // receptor
            "$message", // message
            "$line"   // choose a line number from your account
        );
    }
}
