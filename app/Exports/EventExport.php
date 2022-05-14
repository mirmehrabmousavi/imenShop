<?php

namespace App\Exports;

use App\Models\Event;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class EventExport implements FromCollection
{
    protected $invoices;

    public function __construct($invoices)
    {
        if ($invoices == 'allEvent'){
            $this->invoices = Event::all();
        }
        if ($invoices == 'todayEvent'){
            $this->invoices = Event::whereDate('created_at',Carbon::today())->get();
        }
    }

    public function collection()
    {
        return $this->invoices ;
    }
}
