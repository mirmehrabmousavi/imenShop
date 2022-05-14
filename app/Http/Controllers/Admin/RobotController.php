<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Robot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RobotController extends Controller
{
    public function index(Request $request){
        $showSome =  auth()->user()->getAllPermissions()->where('name' , 'نمایش رباط های خودش')->pluck('name');
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش رباط')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف رباط')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن رباط')->pluck('name');
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
        if(auth()->user()->admin == 1 or count($showSome) >= 1){
            $shows = 1;
        }else{
            $shows = 0;
        }
        if($request->value){
            foreach ($request->value as $value) {
                $tax = Robot::where('id', $value)->first();
            }
            DB::table('robots')->whereIn('id', $request->value)->delete();
        }

        if($request->taxId && !$request->name){
            $taxEdit = Robot::where('id' , $request->taxId)->first();
        }else{
            $taxEdit = '';
        }

        if($request->name || $request->body){
            $request->validate([
                'name' => 'required|max:255',
                'token' => 'required|max:100',
                'group' => 'required|max:30',
            ]);
            if($request->taxId){
                $tax = Robot::where('id' , $request->taxId)->first();
                $tax->update([
                    'body' => $request->body,
                    'name' => $request->name,
                    'status' => $request->status,
                    'group' => $request->group,
                    'token' => $request->token,
                    'data' => $request->datas,
                    'updated_at'=> Carbon::now(),
                ]);
            }else {
                Robot::create([
                    'body' => $request->body,
                    'name' => $request->name,
                    'status' => $request->status,
                    'group' => $request->group,
                    'token' => $request->token,
                    'data' => $request->datas,
                    'user_id' => Auth::id(),
                ]);
            }
        }

        if ($request->search){
            if (count($showSome) >= 1){
                $search = Robot::where('user_id' , auth()->user()->id)->where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = Robot::where('user_id' , auth()->user()->id)->where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }else{
                $search = Robot::where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                if(count($search) == 0){
                    $search = Robot::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
                }
            }
        }else{
            if(count($showSome) >= 1){
                $search = Robot::where('user_id' , auth()->user()->id)->pluck('id')->toArray();
            }else{
                $search = Robot::latest()->pluck('id')->toArray();
            }
        }
        if ($request->date){
            if (count($showSome) >= 1){
                $date = Robot::where('user_id' , auth()->user()->id)->whereDate('created_at',$request->date)->pluck('id')->toArray();
            }else{
                $date = Robot::whereDate('created_at',$request->date)->pluck('id')->toArray();
            }
        }else{
            if(count($showSome) >= 1){
                $date = Robot::where('user_id' , auth()->user()->id)->pluck('id')->toArray();
            }else{
                $date = Robot::latest()->pluck('id')->toArray();
            }
        }
        $arrayFilter = array_intersect($search,$date);
        $taxes = Robot::latest()->whereIn('id' , $arrayFilter)->paginate(30);
        $labels = ['#','آیدی','نام','وضعیت فعالیت','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('RobotPanel' , [
            'taxes' => $taxes,
            'taxEdit' => $taxEdit,
            'labels' => $labels,
            'adds' => $adds,
            'edits' => $edits,
            'deletes' => $deletes,
            'shows' => $shows,
        ]);
    }
    public function setting(Request $request){
        return Inertia::render('Admin/Robot/SettingRobot');
    }
}
