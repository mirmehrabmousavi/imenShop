<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'off',
        'from',
        'to',
    ];

    public function post()
    {
        return $this->morphedByMany(Post::class, 'rankables');
    }
}
