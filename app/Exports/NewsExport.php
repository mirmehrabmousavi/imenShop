<?php

namespace App\Exports;

use App\Models\News;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class NewsExport implements FromCollection
{
    protected $invoices;

    public function __construct($invoices)
    {
        if ($invoices == 'allNews'){
            $this->invoices = News::all();
        }
        if ($invoices == 'todayNews'){
            $this->invoices = News::whereDate('created_at',Carbon::today())->get();
        }
    }

    public function collection()
    {
        return $this->invoices ;
    }
}
