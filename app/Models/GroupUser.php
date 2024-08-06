<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    use HasFactory;

    protected $fillable = [

        'group_id',

        'user_id'

    ];

    public function group() {

        return $this->belongsToMany(Group::class);
        
    }

    public function groups() 
    {

        return  $this->belongsTo(Group::class);

    }

    public function groupUser() {

        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id');
        
    }
    

}
