<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChargeController extends Controller
{
    public function chargeIncrease(){
        $charges = Charge::latest()->where('user_id' , auth()->user()->id)->paginate(60);
        $chargesSuccess = Charge::latest()->where('type' , 0)->where('user_id' , auth()->user()->id)->where('status' , 100)->pluck('price')->sum();
        $chargesFail = Charge::latest()->where('type' , 0)->where('user_id' , auth()->user()->id)->where('status' , '!=' , 100)->pluck('price')->sum();
        $chargesIncrease = Charge::latest()->where('type' , 1)->where('user_id' , auth()->user()->id)->where('status' , 100)->pluck('price')->sum();
        $chargesDecrease = Charge::latest()->where('type' , 1)->where('user_id' , auth()->user()->id)->where('status' , '!=' , 100)->pluck('price')->sum();
        return Inertia::render('Charge/ChargeIndex' , [
            'charges' => $charges,
            'chargesSuccess' => $chargesSuccess,
            'chargesFail' => $chargesFail,
            'chargesIncrease' => $chargesIncrease,
            'chargesDecrease' => $chargesDecrease,
        ]);
    }
}
