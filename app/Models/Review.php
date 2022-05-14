<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable=[
        'body',
        'bodyEn',
        'rate',
        'specifications',
        'ability',
        'colors',
        'size',
    ];

    public function post()
    {
        return $this->morphToMany(Post::class, 'reviewables');
    }
}
