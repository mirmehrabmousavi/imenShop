<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vidget extends Model
{
    use HasFactory;
    use Sluggable;
    protected $fillable=[
        'name',
        'category',
        'title',
        'more',
        'platform',
        'titleEn',
        'moreEn',
        'background',
        'slug',
        'show',
        'type',
        'count',
        'brand',
    ];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
