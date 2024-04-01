<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function Post()
    {
        $users = Post::all();
        $allbikes = Comment::all();
    
        return response()->json([
            'allbikes' => $allbikes,
            'users' => $users
        ]);
    }
}
