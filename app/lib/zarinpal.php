<?php
namespace App\lib;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use nusoap_client;
class zarinpal
{
    public function pay($Amount,$Email,$Mobile,$callback)
    {
        $Description = 'خرید ';  // Required
        $CallbackURL = url($callback); // Required
        $zarinpal = Setting::where('key' , 'zarinpal')->pluck('value')->first();


        $client = new nusoap_client('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
        $client->soap_defencoding = 'UTF-8';
        $result = $client->call('PaymentRequest', [
            [
                'MerchantID'     => $zarinpal,
                'Amount'         => $Amount,
                'Description'    => $Description,
                'Email'          => $Email,
                'Mobile'         => $Mobile,
                'CallbackURL'    => $CallbackURL,
            ],
        ]);

        //Redirect to URL You can do it also by creating a form
        if ($result['Status'] == 100) {
            return $result['Authority'];
        } else {
            return false;
        }



    }
}
