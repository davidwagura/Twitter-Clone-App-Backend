<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public function tweet()
    {
        return $this->belongsTo(Tweet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
        // if ($index !== false) {
        //     unset($explodedLikesId[$index]);
        // }

        // $tweet->likes_id = implode(',', $explodedLikesId);
