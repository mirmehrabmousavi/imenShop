<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'address', 'post', 'code', 'date', 'job' , 'gender', 'residenceAddress'
    ];

    public function user()
    {
        return $this->morphToMany(User::class , 'user_metasables');
    }
}
