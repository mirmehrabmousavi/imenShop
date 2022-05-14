<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'from',
        'name',
        'nameEn',
        'to',
        'day',
    ];

    public function getCreatedAtAttribute($value)
    {
        return verta($value)->format(' H:i | %d / %B / %Y');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function cart()
    {
        return $this->morphedByMany(Cart::class, 'timables');
    }
    public function pay()
    {
        return $this->morphedByMany(Pay::class, 'timables');
    }
    public function post()
    {
        return $this->morphedByMany(Post::class, 'timables');
    }
}
