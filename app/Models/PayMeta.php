<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayMeta extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'post_id',
        'status',
        'pay_id',
        'price',
        'discount_id',
        'method',
        'count',
        'color',
        'size',
    ];
    public function getCreatedAtAttribute($value)
    {
        return verta($value)->format(' H:i | %d / %B / %Y');
    }
    public function pay()
    {
        return $this->belongsTo(Pay::class);
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    public function address()
    {
        return $this->morphToMany(Address::class, 'addressables');
    }
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function guarantee()
    {
        return $this->morphToMany(Guarantee::class, 'guarantables');
    }
}
