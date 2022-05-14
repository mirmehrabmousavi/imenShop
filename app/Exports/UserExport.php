<?php

namespace App\Exports;

use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class UserExport implements FromCollection
{
    protected $invoices;

    public function __construct($invoices , $id)
    {
        if ($invoices == 'allUser'){
            $this->invoices = User::all();
        }
        if ($invoices == 'seller'){
            $this->invoices = Post::where('user_id' , $id)->get();
        }
        if ($invoices == 'todayUser'){
            $this->invoices = User::whereDate('created_at',Carbon::today())->get();
        }
    }

    public function collection()
    {
        return $this->invoices ;
    }
}
