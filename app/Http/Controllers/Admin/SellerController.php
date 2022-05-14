<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Document;
use App\Models\PayMeta;
use App\Models\Post;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SellerController extends Controller
{
    public function index(Request $request)
    {
        if($request->value){
            foreach ($request->value as $value) {
                $tax = User::where('id', $value)->first();
                $tax->revokePermissionTo('فروشنده');
            }
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
            $user = User::where('id' , $request->taxId)->first();
            if($user->document){
                $user->document()->where('status' , 2)->first()->update([
                    'frontNaturalId' => $request->frontImage,
                    'backNaturalId' => $request->backImage,
                ]);
            }else{
                Document::create([
                    'user_id' => $request->taxId,
                    'status' => 0,
                    'frontNaturalId' => $request->frontImage,
                    'backNaturalId' => $request->backImage,
                ]);
            }
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
        if ($request->taxId && !$request->update){
            $sellerEdit = User::latest()->where('id',$request->taxId)->with(["document" => function($q){
                $q->where('status' , 2);
            }])->with('userMeta','company')->first();
        }else{
            $sellerEdit = '';
        }
        if ($request->show){
            $userPost = User::where('id' , $request->show)->with('userMeta','company')->with(["post" => function($q){
                $q->with('review','guarantee')->withCount(["payMeta" => function($q){
                    $q->where('status' , 100);
                }]);
            }])->first();
            $posts = Post::where('user_id' , auth()->user()->id)->where('status' , 1)->pluck('id')->toArray();
            $allPays = PayMeta::whereIn('post_id' , $posts)->where('status' , 100)->pluck('price')->sum();
            $showSeller = [$userPost,$allPays];
        }else{
            $showSeller = [];
        }

        if ($request->search){
            $search = User::where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            if(count($search) == 0){
                $search = User::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            }
        }else{
            $search = User::latest()->pluck('id')->toArray();
        }
        if ($request->sort){
            if($request->sort == 1){
                $sort = User::where('seller' , 0)->pluck('id')->toArray();
            }else{
                $sort = User::where('seller' , 1)->pluck('id')->toArray();
            }
        }else{
            $sort = User::latest()->pluck('id')->toArray();
        }
        $arrayFilter = array_intersect($search,$sort);
        $sellers = User::whereIn('id',$arrayFilter)->permission('فروشنده')->withCount('post')->paginate(60);
        Inertia::setRootView('admin');
        return Inertia::render('SellerPanel' , [
            'sellerEdit' => $sellerEdit,
            'showSeller' => $showSeller,
            'sellers' => $sellers,
        ]);
    }
}
