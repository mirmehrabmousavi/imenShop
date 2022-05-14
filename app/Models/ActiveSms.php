<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveSms extends Model
{
    use HasFactory;
    protected $fillable=[
        'phone',
        'code',
        'expire',
    ];
    public function scopeBuildCode($query){
        do{
            $code = rand(111111,999999);
            $check = ActiveSms::whereCode($code)->first();
        }while($check);
        return $code;
    }
}
