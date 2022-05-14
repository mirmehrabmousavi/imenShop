<?php

namespace App\Exports;

use App\Models\Comment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class CommentExport implements FromCollection
{
    protected $invoices;

    public function __construct($invoices)
    {
        if ($invoices == 'allComment'){
            $this->invoices = Comment::all();
        }
        if ($invoices == 'todayComment'){
            $this->invoices = Comment::whereDate('created_at',Carbon::today())->get();
        }
    }

    public function collection()
    {
        return $this->invoices ;
    }
}
