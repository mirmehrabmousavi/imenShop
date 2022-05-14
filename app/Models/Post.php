<?php

namespace App\Models;

use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Post extends Model implements Feedable
{
    use HasFactory;
    use Sluggable;
    protected $fillable=[
        'title',
        'body',
        'type',
        'file',
        'score',
        'titleEn',
        'bodyEn',
        'status',
        'showcase',
        'used',
        'original',
        'suggest',
        'image',
        'count',
        'variety',
        'slug',
        'price',
        'off',
        'offPrice',
        'user_id',
        'product_id',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function scopeBuildCode($query){
        do{
            $code = rand(111111,999999);
            $check = Post::where('product_id' , $code)->first();
        }while($check);
        return $code;
    }

    public function getCreatedAtAttribute($value)
    {
        return verta($value)->format(' %d / %B / %Y');
    }

    public function toFeedItem(): FeedItem
    {
        if($this->type == 1){
            return FeedItem::create([
                'id' => $this->id,
                'title' => $this->title,
                'summary' => $this->title,
                'updated' => $this->updated_at,
                'link' => '/download-product/'. $this->slug,
                'author' => $this->user->name,
                'image' => $this->image,
            ]);
        }else{
            return FeedItem::create([
                'id' => $this->id,
                'title' => $this->title,
                'summary' => $this->title,
                'updated' => $this->updated_at,
                'link' => '/product/'. $this->slug,
                'author' => $this->user->name,
                'image' => $this->image,
            ]);
        }
    }

    public static function getFeedItems()
    {
        return Post::latest()->where('status' , 1)->take(50)->get();
    }
    public function category()
    {
        return $this->morphToMany(Category::class, 'catables');
    }

    public function post()
    {
        return $this->morphedByMany(Post::class, 'postables');
    }
    public function brand()
    {
        return $this->morphToMany(Brand::class, 'brandables');
    }
    public function like()
    {
        return $this->hasMany(Like::class);
    }
    public function bookmark()
    {
        return $this->hasMany(Bookmark::class);
    }
    public function tag()
    {
        return $this->morphToMany(Tag::class, 'taggables');
    }
    public function guarantee()
    {
        return $this->morphToMany(Guarantee::class, 'guarantables');
    }
    public function carrier()
    {
        return $this->morphToMany(Carrier::class, 'carriables');
    }
    public function time()
    {
        return $this->morphToMany(Time::class, 'timables');
    }
    public function review()
    {
        return $this->morphToMany(Review::class, 'reviewables');
    }
    public function rate()
    {
        return $this->hasMany(Rate::class);
    }
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
    public function view()
    {
        return $this->morphToMany(View::class, 'viewables');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function question()
    {
        return $this->hasMany(Question::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function payMeta()
    {
        return $this->hasMany(PayMeta::class);
    }
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
}
