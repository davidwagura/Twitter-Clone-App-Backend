<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [

        'body',

        'sender_id',

        'receivers_id'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
