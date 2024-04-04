<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tweet;
use App\Models\Comment;
use Illuminate\Http\Request;

class TweetController extends Controller
{

    public function home(){

        $tweets =  Tweet::all();

        return response()->json($tweets);

    }


    public function tweet(Request $request)
    {
        $tweet = new Tweet;
        $tweet->body = $request->body;
        $tweet->user_id = $request->user_id;
        // $tweet->likes = $request->likes;
        // $tweet->retweets = $request->retweets;
        $tweet->save();

        return response()->json($tweet);
    }

    public function user(Request $request)
    {
        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->save();

        return response()->json($user);
    }

    public function comment(Request $request)
    {
        $comment = new Comment;
        $comment->body = $request->body;
        $comment->user_id = $request->user_id;
        $comment->tweet_id = $request->tweet_id;
        $comment->save();

        return response()->json($comment);

    }
    public function showTweet($id)
    {
        $tweet = Tweet::findOrFail($id);
        return response()->json($tweet);
    }

    public function comments($id)
    {
        $comment = Comment::where('tweet_id', $id)->get();
        return response()->json($comment);
    }

    public function userTweets($id)
    {
        // $comments = Comment::where( 'user_id', $id)->with( 'user')->get();
        $comments = Comment::where('tweet_id', $id)->with('tweet')->get();

        return response()->json($comments);
    }
    public function tweetComments($id)
    {
        //('comment')->function name in model relationship
        $tweet = Tweet::with('comment')->findOrFail($id);
        return response()->json($tweet);
    }

    public function likeTweet(Request $request, $id)
    {
        $tweet = Tweet::findOrFail($id);
        $userId = $request->user()->id; // Assuming authenticated user


        $tweet->like($userId);

        return response()->json($tweet);
    }
}