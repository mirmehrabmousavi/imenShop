<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable=[
        'title',
        'type',
        'user_id',
        'description',
    ];

    public function getCreatedAtAttribute($value)
    {
        return verta($value)->format(' H:i | %d / %B / %Y');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
