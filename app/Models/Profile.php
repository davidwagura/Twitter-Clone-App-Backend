<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'name',

        'bio',

        'location',

        'website',

        'birth_date'
    ];


    public function user()
    {
        $this->hasOne(Profile::class);
    }
}
