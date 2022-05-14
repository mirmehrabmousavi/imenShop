<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use Sluggable;

    protected $fillable=[
        'name','nameEn','image','slug','type',
    ];


    public function cats()
    {
        return $this->morphedByMany(Category::class, 'catables');
    }

    public function getCreatedAtAttribute($value)
    {
        return verta($value)->format(' %d / %B / %Y');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function user()
    {
        return $this->morphToMany(User::class, 'catables');
    }
    public function post()
    {
        return $this->morphedByMany(Post::class, 'catables');
    }
    public function news()
    {
        return $this->morphedByMany(News::class, 'catables');
    }
}
