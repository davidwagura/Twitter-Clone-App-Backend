<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'receivers_id'];

    public function message()
    {

        return $this->hasMany(Message::class);
    }
}
