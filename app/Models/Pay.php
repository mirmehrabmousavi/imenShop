<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pay extends Model
{
    use HasFactory;
    protected $fillable=[
        'auth',
        'refId',
        'user_id',
        'price',
        'discount_id',
        'deliver',
        'back',
        'time',
        'track',
        'property',
        'seen',
        'deposit',
        'method',
        'status',
    ];

    /**
     * @inheritDoc
     */
    public function getCreatedAtAttribute($value)
    {
        return verta($value)->format(' H:i | %d / %B / %Y');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function address()
    {
        return $this->morphToMany(Address::class, 'addressables');
    }
    public function post()
    {
        return $this->hasMany(Post::class);
    }
    public function carrier()
    {
        return $this->morphToMany(Carrier::class, 'carriables');
    }
    public function payMeta()
    {
        return $this->hasMany(PayMeta::class);
    }
}
