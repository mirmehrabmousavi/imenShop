<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Document;
use App\Models\Event;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SellerController extends Controller
{
    public function becomeSeller(Request $request){
        if ($request->level == 1){
            if($request->seller){
                $request->validate([
                    'post' => 'required|min:10|max:10',
                    'residenceAddress' => 'required|max:255',
                    'landlinePhone' => 'required|max:11',
                    'number' => 'required',
                    'email' => 'required',
                    'companyName' => 'required|max:150',
                    'registrationNumber' => 'required|min:12|max:12',
                    'nationalID' => 'required|min:11|max:11',
                    'signatureOwners' => 'required|max:255',
                    'economicCode' => 'required|min:12|max:12',
                    'shaba' => 'required|max:26',
                    'userName' => 'required|max:255|unique:users,name',
                    'seller' => 'required',
                    'type' => 'required',
                ]);
                auth()->user()->update([
                    'name' => $request->userName,
                    'seller' => $request->seller,
                    'shaba' => $request->shaba,
                    'landlinePhone'=>$request->landlinePhone,
                ]);
                $check = Auth::user()->company()->count();
                if ($check >= 1){
                    Auth::user()->company()->update([
                        'name' => $request->companyName,
                        'type' => $request->type,
                        'registration' => $request->registrationNumber,
                        'NationalID' => $request->nationalID,
                        'economicCode' => $request->economicCode,
                        'signer' => $request->signatureOwners,
                        'residenceAddress' => $request->residenceAddress,
                    ]);
                    Event::create([
                        'type' => 11,
                        'title' => 'تغییر اطلاعات کاربر',
                        'user_id' => auth()->user()->id,
                        'description' => 'کاربر با نام ' . auth()->user()->name . 'اطلاعات خود را تغییر داد',
                    ]);
                }
                else{
                    Company::create([
                        'name' => $request->companyName,
                        'type' => $request->type,
                        'registration' => $request->registrationNumber,
                        'NationalID' => $request->nationalID,
                        'economicCode' => $request->economicCode,
                        'signer' => $request->signatureOwners,
                        'residenceAddress' => $request->residenceAddress,
                        'user_id' => auth()->user()->id,
                    ]);
                    Event::create([
                        'type' => 12,
                        'title' => 'تکمیل اطلاعات کاربر',
                        'user_id' => auth()->user()->id,
                        'description' => 'کاربر با نام ' . auth()->user()->name . 'اطلاعات خود را تکمیل کرد',
                    ]);
                }
            }else{
                $request->validate([
                    'code' => 'required|min:10|max:11',
                    'name' => 'required|max:255',
                    'post' => 'required|min:10|max:10',
                    'dateBirth' => 'required|max:11',
                    'residenceAddress' => 'required|max:255',
                    'landlinePhone' => 'required|min:10|max:11',
                    'number' => 'required',
                    'email' => 'required',
                    'shaba' => 'required|max:26',
                    'userName' => 'required|max:255',
                    'seller' => 'required',
                    'gender' => 'required',
                ]);
                auth()->user()->update([
                    'name' => $request->userName,
                    'shaba' => $request->shaba,
                    'seller' => $request->seller,
                    'landlinePhone'=>$request->landlinePhone,
                ]);
                $check = Auth::user()->userMeta()->count();
                if ($check >= 1){
                    Auth::user()->userMeta()->first()->update([
                        'date'=>$request->dateBirth,
                        'name'=>$request->name,
                        'post'=>$request->post,
                        'gender'=>$request->gender,
                        'code'=>$request->code,
                        'residenceAddress' => $request->residenceAddress,
                    ]);
                    Event::create([
                        'type' => 11,
                        'title' => 'تغییر اطلاعات کاربر',
                        'user_id' => auth()->user()->id,
                        'description' => 'کاربر با نام ' . auth()->user()->name . 'اطلاعات خود را تغییر داد',
                    ]);
                }
                else{
                    $userMeta = UserMeta::create([
                        'date'=>$request->dateBirth,
                        'name'=>$request->name,
                        'post'=>$request->post,
                        'landlinePhone'=>$request->landlinePhone,
                        'gender'=>$request->gender,
                        'code'=>$request->code,
                        'residenceAddress' => $request->residenceAddress,
                    ]);
                    Event::create([
                        'type' => 12,
                        'title' => 'تکمیل اطلاعات کاربر',
                        'user_id' => auth()->user()->id,
                        'description' => 'کاربر با نام ' . auth()->user()->name . 'اطلاعات خود را تکمیل کرد',
                    ]);
                    Auth::user()->userMeta()->sync($userMeta->id);
                }
            }
            $levels = 2;
        }elseif($request->level == 2){
            $request->validate([
                'frontImage' => 'required|max:100',
                'backImage' => 'required|max:100',
                'number' => 'required',
                'email' => 'required',
            ]);
            Document::create([
                'status' => 0,
                'frontNaturalId' => $request->frontImage,
                'backNaturalId' => $request->backImage,
                'user_id' => auth()->user()->id,
            ]);
            Event::create([
                'type' => 14,
                'title' => 'ارسال مدارک',
                'user_id' => auth()->user()->id,
                'description' => 'کاربر با نام ' . auth()->user()->name . 'مدارک خود را ارسال کرد',
            ]);
            $levels = 3;
        }elseif($request->level == 3){
            $request->validate([
                'number' => 'required',
                'email' => 'required',
            ]);
            $check = Document::latest()->where('status' , 2)->where('user_id' , auth()->user()->id)->first();
            if($check){
                auth()->user()->givePermissionTo('فروشنده');
            }
            $levels = 4;
        }else{
            $documents = Document::latest()->where('user_id' , auth()->user()->id)->get();
            if(count($documents) >= 1){
                $showSome =  auth()->user()->getAllPermissions()->where('name' , 'فروشنده')->pluck('name')->first();
                if($showSome){
                    $levels = 4;
                }else{
                    $levels = 3;
                }
            }else{
                $levels = 1;
            }
        }
        $users = User::where('id' , auth()->user()->id)->with('userMeta','company')->first();
        $documents = Document::latest()->where('user_id' , auth()->user()->id)->get();
        return Inertia::render('User/BecomeSeller', [
            'documents' => $documents,
            'users' => $users,
            'levels' => $levels,
        ]);
    }
}
