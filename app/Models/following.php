<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class following extends Model
{
    use HasFactory;

    public function followers()
    {
        return $this->belongsToMany(Followers::class);
    }
}
