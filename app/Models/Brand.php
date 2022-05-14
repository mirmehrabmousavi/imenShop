<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    use Sluggable;
    protected $fillable=[
        'name','nameEn','image','slug'
    ];
    /**
     * @inheritDoc
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function getCreatedAtAttribute($value)
    {
        return verta($value)->format(' %d / %B / %Y');
    }

    public function user()
    {
        return $this->morphToMany(User::class, 'brandables');
    }
    public function post()
    {
        return $this->morphedByMany(Post::class, 'brandables');
    }
}
