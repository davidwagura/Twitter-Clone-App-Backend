<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasFactory,HasApiTokens;

    protected $fillable = 
    [
        // 'first_name',
        // Session::flash('message', 'tweet deleted successfully');
        // 'last_name',
        // 'email',
        // 'username',
        'password',
        'password_confirmation'
    ];

    public function tweet()
    {
        return $this->hasMany(Tweet::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }
}
