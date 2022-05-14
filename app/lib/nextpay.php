<?php
namespace App\lib;
use App\Models\Setting;
use GuzzleHttp\Client;

class nextpay
{
    public function pay($Amount,$Email,$Mobile,$callback)
    {
        $nextPay = Setting::where('key' , 'nextPay')->pluck('value')->first();
        $client = new Client();
        $response = $client->request('POST', 'https://nextpay.org/nx/gateway/token',
            [
                'form_params' => [
                    'api_key' => $nextPay,
                    'amount' => $Amount,
                    'order_id' => '85NX85s427',
                    'customer_phone' => $Mobile,
                    'callback_uri' => url($callback)
                ],
                'allow_redirects' => true
            ]);
        $contents = $response->getBody()->getContents();
        $contents = json_decode($contents,true);
        return $contents['trans_id'];
    }
}
