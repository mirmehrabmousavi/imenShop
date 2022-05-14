<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Models\PayMeta;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request){
        if ($request->update){
            Checkout::where('id' , $request->taxId)->first()->update([
                'status' => $request->status,
                'price' => $request->price,
                'shaba' => $request->shaba,
            ]);
        }
        if($request->value){
            DB::table('checkouts')->whereIn('id', $request->value)->delete();
        }
        if($request->taxId && !$request->update){
            $checkEdit = Checkout::where('id', $request->taxId)->with('user')->first();
        }else{
            $checkEdit = [];
        }

        if ($request->search){
            $search = Checkout::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
        }else{
            $search = Checkout::latest()->pluck('id')->toArray();
        }
        if ($request->sort){
            if($request->sort == 1){
                $sort = Checkout::where('status' , 0)->pluck('id')->toArray();
            }
            if($request->sort == 2){
                $sort = Checkout::where('status' , 1)->pluck('id')->toArray();
            }
            if($request->sort == 3){
                $sort = Checkout::where('status' , 2)->pluck('id')->toArray();
            }
        }else{
            $sort = Checkout::latest()->pluck('id')->toArray();
        }
        $arrayFilter = array_intersect($search,$sort);
        $checkouts = Checkout::latest()->whereIn('id',$arrayFilter)->with('user')->paginate(100);
        $labels = ['#','آیدی','کاربر','مبلغ','وضعیت تسویه','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('CheckoutPanel' , [
            'labels' => $labels,
            'checkEdit' => $checkEdit,
            'checkouts' => $checkouts,
        ]);
    }
}
