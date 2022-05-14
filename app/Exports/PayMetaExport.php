<?php

namespace App\Exports;

use App\Models\PayMeta;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class PayMetaExport implements FromCollection
{
    protected $invoices;

    public function __construct($invoices)
    {
        if ($invoices == 'allPayMeta'){
            $this->invoices = PayMeta::all();
        }
        if ($invoices == 'todayPayMeta'){
            $this->invoices = PayMeta::whereDate('created_at',Carbon::today())->get();
        }
    }

    public function collection()
    {
        return $this->invoices ;
    }
}
