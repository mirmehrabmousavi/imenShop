<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function getAll(){
        return auth()->user()->address()->latest()->select(['name','id','status','post','number','address'])->get();
    }

    public function selectAddress(Request $request){
        foreach (auth()->user()->address()->where('status', 1)->get() as $value) {
            Address::where('id' , $value['id'])->update([
                'status' => 0
            ]);
        }
        auth()->user()->address()->where('id' , $request->address)->update([
            'status' => 1
        ]);
        return 'success';
    }

    public function deleteAddress(Request $request){
        $address = auth()->user()->address()->where('id' , $request->address)->first();
        $address->pay()->detach();
        $address->user()->detach();
        $address->delete();
        return 'success';
    }

    public function create(Request $request){
        $request->validate([
            'geo' => 'required',
            'name' => 'required|max:50',
            'address' => 'required|max:255',
            'post' => 'required|max:10|min:10',
            'state' => 'required',
            'city' => 'required',
            'plaque' => 'required|max:4',
            'number' => 'required|min:11|max:11',
        ]);
        foreach (auth()->user()->address()->where('status', 1)->get() as $value) {
            Address::where('id' , $value['id'])->update([
                'status' => 0
            ]);
        }
        $geo=[
            'lng'=> $request->geo[0],
            'lat'=> $request->geo[1],
        ];
        $address = Address::create([
            'geo'=> json_encode($geo),
            'name'=> $request->name,
            'address'=> $request->address,
            'post'=> $request->post,
            'state'=> $request->state,
            'city'=> $request->city,
            'plaque'=> $request->plaque,
            'number'=> $request->number,
            'status'=> 0,
        ]);
        auth()->user()->address()->attach($address->id);
        return $address;
    }

    public function editUserAddress(Request $request){
        $request->validate([
            'geo' => 'required',
            'name' => 'required|max:50',
            'address' => 'required|max:255',
            'post' => 'required|max:10|min:10',
            'state' => 'required',
            'city' => 'required',
            'plaque' => 'required|max:4',
            'number' => 'required|min:11|max:11',
        ]);
        DB::table('addresses')->where('status', 1)->update([
            'status' => 0
        ]);
        $geo=[
            'lng'=> $request->geo[0],
            'lat'=> $request->geo[1],
        ];
        auth()->user()->address()->where('id' , $request->address_id)->first()->update([
            'geo'=> json_encode($geo),
            'name'=> $request->name,
            'address'=> $request->address,
            'post'=> $request->post,
            'state'=> $request->state,
            'city'=> $request->city,
            'plaque'=> $request->plaque,
            'number'=> $request->number,
            'status'=> 1,
        ]);
        return 'success';
    }

    public function editAddress(Request $request){
        return auth()->user()->address()->where('id' , $request->address)->first();
    }
}
