<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function store(Request $request){
        $user = auth()->user();
        if (!$user){
            return 'noUser';
        }else{
            Ticket::create([
                'body' => $request->ticket,
                'user_id' => auth()->user()->id,
            ]);
            Event::create([
                'type' => 8,
                'title' => 'تیکت',
                'user_id' => auth()->user()->id,
                'description' => 'کاربر با نام ' . auth()->user()->name . 'تیکتی ارسال کرد',
            ]);
            return 'success';
        }
    }
}
