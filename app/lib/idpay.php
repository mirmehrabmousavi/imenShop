<?php
namespace App\lib;
use App\Models\Setting;
use GuzzleHttp\Client;

class idpay
{
    public function pay($Amount,$Email,$Mobile,$callback)
    {
        $idpay = Setting::where('key' , 'idpay')->pluck('value')->first();
        $params = array(
            'order_id' => '101',
            'amount' => $Amount,
            'mail' => $Email,
            'phone' => $Mobile,
            'desc' => 'خرید',
            'callback' => url($callback),
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            "X-API-KEY:$idpay",
            'X-SANDBOX: 1'
        ));


        $result = curl_exec($ch);
        curl_close($ch);
        $contents = json_decode($result,true);
        if ($contents['id']){
            return $contents['id'];
        }else{
            return false;
        }
    }
}
