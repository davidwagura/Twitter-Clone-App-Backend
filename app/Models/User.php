<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'password_confirmation',
        'followings_id',
        'followers_id'
    ];

    public function tweets()
    {
        return $this->hasMany(Tweet::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function commentComments()
    {
        return $this->hasMany(CommentComment::class);
    }

    public function groups() 
    {
        return $this->belongsToMany(Group::class);
    }
    
    public function createdGroups()
    {
        return $this->hasMany(Group::class, 'creator_id');
    }
}
