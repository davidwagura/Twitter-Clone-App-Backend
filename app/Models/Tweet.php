<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    use HasFactory;


    protected $fillable = [
        'body',

        'user_id',

        'image_path',

        'likes_id',

        'retweet_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isLikedByUser($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isRetweetedByUser($userId)
    {
        return $this->retweets()->where('user_id', $userId)->exists();
    }
}
