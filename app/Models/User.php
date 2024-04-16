<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        // 'first_name',
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
