<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index(Request $request){
        DB::table('tickets')->where('seen', 0)->update(['seen' => 1]);
        if ($request->sort == 0){
            if ($request->search){
                if ($request->date){
                    $name = Auth::user()->where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->first();
                    $tickets = Ticket::latest()->where('user_id' , $name)->whereDate('created_at',$request->date)->with('user')->paginate(25);
                }else{
                    $name = Auth::user()->where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->first();
                    $tickets = Ticket::latest()->where('user_id' , $name)->with('user')->paginate(25);
                }
            }else{
                if ($request->date){
                    $tickets = Ticket::latest()->whereDate('created_at',$request->date)->with('user')->paginate(25);
                }else{
                    $tickets = Ticket::latest()->with('user')->paginate(25);
                }
            }
        }
        else if($request->sort == 1){
            if ($request->search){
                if ($request->date){
                    $name = Auth::user()->where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->first();
                    $tickets = Ticket::latest()->where('user_id' , $name)->where('answer', '!=' , null)->whereDate('created_at',$request->date)->with('user')->paginate(25);
                }else{
                    $name = Auth::user()->where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->first();
                    $tickets = Ticket::latest()->where('user_id' , $name)->where('answer', '!=' , null)->with('user')->paginate(25);
                }
            }else{
                if ($request->date){
                    $tickets = Ticket::latest()->where('answer' , '!=' , null)->whereDate('created_at',$request->date)->with('user')->paginate(25);
                }else{
                    $tickets = Ticket::latest()->where('answer' , '!=' , null)->with('user')->paginate(25);
                }
            }
        }
        else if($request->sort == 2){
            if ($request->search){
                if ($request->date){
                    $name = Auth::user()->where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->first();
                    $tickets = Ticket::latest()->where('user_id' , $name)->where('answer' , null)->whereDate('created_at',$request->date)->with('user')->paginate(25);
                }else{
                    $name = Auth::user()->where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->first();
                    $tickets = Ticket::latest()->where('user_id' , $name)->where('answer' , null)->whereDate('created_at',$request->date)->with('user')->paginate(25);
                }
            }else{
                if ($request->date){
                    $tickets = Ticket::latest()->where('answer' , null)->whereDate('created_at',$request->date)->with('user')->paginate(25);
                }else{
                    $tickets = Ticket::latest()->where('answer' , null)->with('user')->paginate(25);
                }
            }
        }
        Inertia::setRootView('admin');
        return Inertia::render('TicketPanel' , [
            'tickets' => $tickets,
        ]);
    }

    public function edit(Ticket $ticket){
        return $ticket;
    }

    public function update(Ticket $ticket , Request $request){
        $request->validate([
            'body' => 'required',
        ]);
        $ticket->update([
            'body'=> $request->body,
            'answer'=> $request->answer,
            'updated_at'=> Carbon::now(),
        ]);
        return redirect('/admin/ticket');
    }

    public function removeArray(Request $request){
        DB::table('tickets')->whereIn('id', $request->value)->delete();
        return redirect('/admin/ticket');
    }
}
