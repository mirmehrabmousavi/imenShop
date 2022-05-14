<?php

namespace App\Exports;

use App\Models\Ticket;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class TicketExport implements FromCollection
{
    protected $invoices;

    public function __construct($invoices)
    {
        if ($invoices == 'allTicket'){
            $this->invoices = Ticket::all();
        }
        if ($invoices == 'todayTicket'){
            $this->invoices = Ticket::whereDate('created_at',Carbon::today())->get();
        }
    }

    public function collection()
    {
        return $this->invoices ;
    }
}
