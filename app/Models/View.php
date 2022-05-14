<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_ip',
        'browser',
        'platform',
    ];
    public function getCreatedAtAttribute($value)
    {
        return verta($value)->format(' %d / %B / %Y');
    }
    public function post()
    {
        return $this->morphedByMany(Post::class, 'viewables');
    }
    public function user()
    {
        return $this->morphedByMany(User::class, 'viewables');
    }
}
