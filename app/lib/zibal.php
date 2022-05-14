<?php
namespace App\lib;
use App\Models\Setting;
use GuzzleHttp\Client;

class zibal
{
    public function pay($Amount,$Email,$Mobile,$callback)
    {
        $zibal = Setting::where('key' , 'zibal')->pluck('value')->first();
        $client = new Client();
        $response = $client->request('POST', 'https://gateway.zibal.ir/v1/request',
            [
                'form_params' => [
                    'merchant' => $zibal,
                    'description' => 'خرید',
                    'amount' => $Amount,
                    'mobile' => $Mobile,
                    'callbackUrl' => url($callback)
                ],
                'allow_redirects' => true
            ]);
        $contents = $response->getBody()->getContents();
        $contents = json_decode($contents,true);
        if ($contents['result'] == 100) {
            return $contents['trackId'];
        } else {
            return false;
        }
    }
}
