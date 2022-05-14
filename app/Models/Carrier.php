<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    use HasFactory;
    protected $fillable=[
        'name','nameEn','price','city','limit'
    ];

    public function getCreatedAtAttribute($value)
    {
        return verta($value)->format(' %d / %B / %Y');
    }

    public function user()
    {
        return $this->morphToMany(User::class, 'carriables');
    }
    public function post()
    {
        return $this->morphedByMany(Post::class, 'carriables');
    }
    public function pay()
    {
        return $this->morphedByMany(Pay::class, 'carriables');
    }
}
