<?php

namespace App\Exports;

use App\Models\Pay;
use App\Models\PayMeta;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class PayExport implements FromCollection
{
    protected $invoices;

    public function __construct($invoices, $pay)
    {
        if ($invoices == 'allPay'){
            $this->invoices = Pay::all();
        }
        if ($invoices == 'pay'){
            $this->invoices = PayMeta::where('pay_id' , $pay)->with('post')->get();
        }
        if ($invoices == 'todayPay'){
            $this->invoices = Pay::whereDate('created_at',Carbon::today())->get();
        }
    }

    public function collection()
    {
        return $this->invoices ;
    }
}
