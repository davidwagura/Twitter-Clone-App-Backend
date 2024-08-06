<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_path',
        'creator_id',
        'member_id'
    ];

    public function users()//attach detach
    {
        return $this->hasMany(User::class);
    }
    
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function profile()
    {
        return $this->belongsToMany(Profile::class);
    }
}
