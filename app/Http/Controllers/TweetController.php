<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use App\Models\Comment;
use Illuminate\Http\Request;

class TweetController extends Controller
{
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

    public function tweetComments($id)
    {
        $comment = Comment::where('tweet_id', $id)->get();
        return response()->json($comment);
    }
}
