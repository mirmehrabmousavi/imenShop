<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ChargeController extends Controller
{
    public function index(Request $request) {
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش شارژ')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف شارژ')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن شارژ')->pluck('name');
        if(auth()->user()->admin == 1 or count($checkEdits) >= 1){
            $edits = 1;
        }else{
            $edits = 0;
        }
        if(auth()->user()->admin == 1 or count($checkDeletes) >= 1){
            $deletes = 1;
        }else{
            $deletes = 0;
        }
        if(auth()->user()->admin == 1 or count($checkAdds) >= 1){
            $adds = 1;
        }else{
            $adds = 0;
        }
        if($request->value){
            DB::table('charges')->whereIn('id', $request->value)->delete();
        }
        if($request->price){
            $request->validate([
                'price' => 'required|max:255',
            ]);
            if($request->chargeId){
                $charge = Charge::where('id' , $request->chargeId)->first();
                $charge->update([
                    'price'=> $request->price,
                    'type'=> $request->type,
                    'status'=> $request->status,
                    'user_id'=> $request->user,
                    'updated_at'=> Carbon::now(),
                ]);
            }else{
                $chars = '012345678901234567890123456789';
                $chars2 = substr(str_shuffle($chars), 0 , 9);
                if (Charge::where('property' , $chars2)->first()){
                    $chars2 = substr(str_shuffle($chars), 0 , 9);
                    if (Charge::where('property' , $chars2)->first()){
                        $chars2 = substr(str_shuffle($chars), 0 , 9);
                    }
                }
                $charge = Charge::create([
                    'price'=> $request->price,
                    'type'=> $request->type,
                    'status'=> $request->status,
                    'property'=> $chars2,
                    'user_id'=> $request->user,
                ]);
            }
        }
        if($request->chargeId && !$request->price){
            $chargeEdit = Charge::where('id' , $request->chargeId)->with('user')->first();
        }else{
            $chargeEdit = '';
        }
        if ($request->search){
            $user = User::where("name" , "LIKE" , "%{$request->search}%")->first();
            $search = $user->charge()->pluck('id')->toArray();
            if(count($search) == 0){
                $search = Charge::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            }
        }else{
            $search = Charge::latest()->pluck('id')->toArray();
        }
        if ($request->date){
            $date = Charge::whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = Charge::latest()->pluck('id')->toArray();
        }
        $arrayFilter = array_intersect($search,$date);
        $charges = Charge::latest()->whereIn('id' , $arrayFilter)->with('user')->paginate(30);
        $users = User::latest()->take(200)->get();
        $labels = ['#','کاربر','مبلغ','شماره سفارش','نوع شارژ','وضعیت پرداخت','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('ChargePanel' , [
            'users' => $users,
            'adds' => $adds,
            'edits' => $edits,
            'labels' => $labels,
            'deletes' => $deletes,
            'chargeEdit' => $chargeEdit,
            'charges' => $charges,
        ]);
    }
}
