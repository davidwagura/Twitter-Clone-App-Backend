<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    use HasFactory;



    protected $casts = [
        'likes_id' => 'array',
    ];

    public function like($userId)
    {
        if (!in_array($userId, $this->likes_id)) {
            $this->likes_id[] = $userId;
            $this->save();
        }
    }




    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }
}
