<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Document;
use App\Models\Event;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function index(Request $request){
        if($request->value){
            DB::table('documents')->whereIn('id', $request->value)->delete();
        }
        if ($request->update){
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|max:255',
                'number' => 'required|max:255',
                'landlinePhone' => 'required|min:10|max:10',
                'shaba' => 'required|max:26',
                'frontImage' => 'required|max:100',
                'backImage' => 'required|max:100',
            ]);
            $user1 = Document::latest()->where('id' , $request->taxId)->pluck('user_id')->first();
            $user = User::where('id' , $user1)->first();
            Document::where('id' , $request->taxId)->first()->update([
                'status' => $request->status,
                'frontNaturalId' => $request->frontImage,
                'backNaturalId' => $request->backImage,
            ]);
            $user->update([
                'name' => $request->userName,
                'number' => $request->number,
                'email' => $request->email,
                'shaba' => $request->shaba,
                'seller' => $request->seller,
                'landlinePhone'=>$request->landlinePhone,
            ]);
            $user->update([
                'name' => $request->userName,
                'shaba' => $request->shaba,
                'seller' => $request->seller,
                'landlinePhone'=>$request->landlinePhone,
            ]);
            if ($request->seller){
                $request->validate([
                    'post' => 'required|min:10|max:10',
                    'residenceAddress' => 'required|max:255',
                    'landlinePhone' => 'required|max:10',
                    'number' => 'required',
                    'email' => 'required',
                    'companyName' => 'required|max:150',
                    'registrationNumber' => 'required|min:12|max:12',
                    'nationalID' => 'required|min:11|max:11',
                    'signatureOwners' => 'required|max:255',
                    'economicCode' => 'required|min:12|max:12',
                    'shaba' => 'required|max:26',
                    'userName' => 'required|max:255',
                    'seller' => 'required',
                    'type' => 'required',
                ]);
                $check = $user->company()->count();
                if ($check >= 1){
                    $user->company()->update([
                        'name' => $request->companyName,
                        'type' => $request->type,
                        'registration' => $request->registrationNumber,
                        'NationalID' => $request->nationalID,
                        'economicCode' => $request->economicCode,
                        'signer' => $request->signatureOwners,
                        'residenceAddress' => $request->residenceAddress,
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
                        'user_id' => $user->id,
                    ]);
                }
            }
            else{
                $request->validate([
                    'code' => 'required|min:10|max:11',
                    'name' => 'required|max:255',
                    'post' => 'required|min:10|max:10',
                    'dateBirth' => 'required|max:11',
                    'residenceAddress' => 'required|max:255',
                    'landlinePhone' => 'required|min:10|max:10',
                    'number' => 'required',
                    'email' => 'required',
                    'shaba' => 'required|max:26',
                    'userName' => 'required|max:255',
                    'seller' => 'required',
                    'gender' => 'required',
                ]);
                $check = $user->userMeta()->count();
                if ($check >= 1){
                    $user->userMeta()->first()->update([
                        'date'=>$request->dateBirth,
                        'name'=>$request->name,
                        'post'=>$request->post,
                        'gender'=>$request->gender,
                        'code'=>$request->code,
                        'residenceAddress' => $request->residenceAddress,
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
                    $user->userMeta()->sync($userMeta->id);
                }
            }
        }
        if ($request->taxId){
            $documentEdit = Document::latest()->where('id',$request->taxId)->with(["user" => function($q){
                $q->with('userMeta' , 'company');
            }])->first();
        }else{
            $documentEdit = '';
        }
        $documents = Document::latest()->with('user')->paginate(100);
        $labels = ['کاربر','تصویر پشت کارت ملی','تصویر جلو کارت ملی','وضعیت بررسی','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('DocumentPanel' , [
            'documentEdit' => $documentEdit,
            'labels' => $labels,
            'documents' => $documents,
        ]);
    }
}
