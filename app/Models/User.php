<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\MailResetPasswordToken;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'buy',
        'admin',
        'number',
        'shaba',
        'landlinePhone',
        'password',
        'seller',
        'activity',
        'profile',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */

    public function getCreatedAtAttribute($value)
    {
        return verta($value)->format(' %d / %B / %Y');
    }

    public function ticket()
    {
        return $this->hasMany(Ticket::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function charge()
    {
        return $this->hasMany(Charge::class);
    }

    public function checkout()
    {
        return $this->hasMany(Checkout::class);
    }

    public function document()
    {
        return $this->hasMany(Document::class);
    }

    public function event()
    {
        return $this->hasMany(Event::class);
    }

    public function address()
    {
        return $this->morphToMany(Address::class, 'addressables');
    }

    public function category()
    {
        return $this->morphToMany(Category::class, 'catables');
    }

    public function guarantee()
    {
        return $this->morphToMany(Guarantee::class, 'guarantables');
    }

    public function tag()
    {
        return $this->morphToMany(Tag::class, 'taggables');
    }

    public function brand()
    {
        return $this->morphToMany(Brand::class, 'brandables');
    }
    public function view()
    {
        return $this->morphToMany(View::class, 'viewables');
    }
    public function gallery()
    {
        return $this->hasMany(Gallery::class);
    }
    public function bookmark()
    {
        return $this->hasMany(Bookmark::class);
    }
    public function like()
    {
        return $this->hasMany(Like::class);
    }
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
    public function carrier()
    {
        return $this->morphToMany(Carrier::class, 'carriables');
    }
    public function time()
    {
        return $this->morphToMany(Time::class, 'timables');
    }
    public function report()
    {
        return $this->hasMany(Report::class);
    }
    public function notification()
    {
        return $this->hasMany(Notification::class);
    }
    public function question()
    {
        return $this->hasMany(Question::class);
    }
    public function rate()
    {
        return $this->hasMany(Rate::class);
    }
    public function post()
    {
        return $this->hasMany(Post::class);
    }
    public function news()
    {
        return $this->hasMany(News::class);
    }
    public function robot()
    {
        return $this->hasMany(Robot::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function userMeta()
    {
        return $this->morphToMany(UserMeta::class , 'user_metasables');
    }
    public function payMeta()
    {
        return $this->hasMany(PayMeta::class);
    }
    public function pay()
    {
        return $this->hasMany(Pay::class);
    }
}
