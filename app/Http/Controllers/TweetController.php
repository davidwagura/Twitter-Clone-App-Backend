<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tweet;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TweetController extends Controller
{

    public function home(){

        $tweets =  Tweet::all();

        return response()->json($tweets);

    }

    public function tweet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required',
            'user_id' => ['required', 'integer', function ($attribute, $value, $fail) {
                if (!is_int($value)) {
                    $fail('The ' . $attribute . ' must be an integer.');
                }  
            }]
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
            ]);
        }
    
        $tweet = Tweet::create([
            'body' => $request->body,
            'user_id' => $request->user_id
        ]);
    


            return response()->json([
                'status' => $tweet ? true : false,
                'message' => $tweet ? 'Tweet created successfully' : 'Validation failed'
            ],200);
    }
    
    public function user(Request $request)
    {
        $user = new User;

        $user->first_name = $request->first_name;

        $user->last_name = $request->last_name;

        $user->email = $request->email;

        $user->username = $request->username;

        $user->password = $request->password;

        $user->save();

        return response()->json(['message' => 'User created successfully'],200);
    }

    public function comment(Request $request)
    {
        $comment = new Comment;

        $comment->body = $request->body;

        $comment->user_id = $request->user_id;

        $comment->tweet_id = $request->tweet_id;

        $comment->save();

        return response()->json(['message' => 'Comment created successfully'],200);

    }
    public function showTweet($id) //get
    {
        $tweet = Tweet::findOrFail($id);

        return response()->json($tweet);
    }

    public function comments($id) //get
    {
        $comment = Comment::where('tweet_id', $id)->get();

        return response()->json($comment);
    }

    public function userTweets($id) //get
    {
        // $comments = Comment::where( 'user_id', $id)->with( 'user')->get();

        $comments = Comment::where('tweet_id', $id)->with('tweet')->get();

        return response()->json($comments);
    }
    public function tweetComments($id) //get
    {
        $tweet = Tweet::with('comment')->findOrFail($id);

        return response()->json($tweet);
    }

    public function likeTweet($tweet_id, $user_id)
    {
        $tweet = Tweet::findOrFail($tweet_id);
    
        $likesId = $tweet->likes_id;
    
        if (!empty($likesId)) {

            $likesId = explode(',', $likesId);

            if (!in_array($user_id, $likesId)) {

                $likesId[] = $user_id;

                $tweet->likes_id = implode(',', $likesId);

                $tweet->likes++;
            }

        } else {

            $tweet->likes_id = $user_id;

            $tweet->likes = 1;

        }
    
        $tweet->save();
    
        return response()->json(['message' => 'Tweet liked successfully'],200);
    }
    

    public function unlikeTweet($tweet_id, $user_id)
    {
        $tweet = Tweet::findOrFail($tweet_id);
        
        $likesId = $tweet->likes_id;
        
        if (!empty($likesId)) {

            $explodedLikesId = explode(',', $likesId);

            $index = array_search(strval($user_id), $explodedLikesId);
            
            if ($index !== false) {

                unset($explodedLikesId[$index]);

                $tweet->likes_id = implode(',', $explodedLikesId);

                $tweet->likes = max(0, $tweet->likes - 1);

            }
            // If likes array is empty, set likes to zero
            if (empty($explodedLikesId)) {

                $tweet->likes = 0;

            }
        } else {

            $tweet->likes = 0;
        }
    
        $tweet->save();
    
        return response()->json(['message' => 'Tweet unliked successfully'],200);
    }
    
    public function retweet($tweet_id, $user_id)
    {
        $tweet = Tweet::findOrFail($tweet_id);
    
        $retweetsId = $tweet->retweets_id;
    
        if(!empty($retweetsId)){

            $retweetsId = explode(',' , $retweetsId);

            if(!in_array($user_id, $retweetsId)){

                $retweetsId[] = $user_id;

                $tweet->retweets_id = implode(',', $retweetsId);

                $tweet->retweets++;
            }

        } else {

            $tweet->retweets_id = $user_id;

            $tweet->retweets = 1;
        }
    
        $tweet->save();
    
        return response()->json(['message' => 'Retweet successful'],200);
    }
    
    public function unretweet($tweet_id, $user_id)
    {
        $tweet = Tweet::findOrFail($tweet_id);
    
        $retweetsId = $tweet->retweets_id;
    
        if (!empty($retweetsId)) {

            $explodedRetweetsId = explode(',' , $retweetsId);
            
            $index = array_search(strval($user_id), $explodedRetweetsId);
    
            if ($index !== false) {

                unset($explodedRetweetsId[$index]);

                $tweet->retweets_id = implode(',', $explodedRetweetsId);

                $tweet->retweets = max(0, count($explodedRetweetsId));
            }
        }
        $tweet->save();
    
        return response()->json(['message' => 'Unretweet successful'],200);
    }

    public function deleteTweet($tweet_id) //get
    {
        $tweet = Tweet::findOrFail($tweet_id);

        $count = $tweet->comments()->count(); 

        // \Log::debug($count);

        if($count > 1)
        {

            return response()->json(['message' => 'You can not delete the tweet with comments'],403) ;

        } else {

            $tweet->comments()->delete();

            $tweet->delete();

            return response()->json($tweet);

        }

    }

    public function resetPassword(Request $request, $user_id)
    {
        $request->validate([

            'password' => 'required|min:6|confirmed',

        ]);
    
        $user = User::findOrFail($user_id);

        $user->password = bcrypt($request->password);

        $user->save();
    
        return response()->json(['message' => 'Password reset successfully'],200);
    }

    public function login(Request $request)
    {
        $request->validate([

            'email' => 'required|string|email',

            'password' => 'required|string',

        ]);

        $credentials = $request->only('email', 'password');

        if(Auth::attempt($credentials))

        {
            $user = Auth::user();

            return response()->json([

                'user' => $user,

                'authorization' => [

                    'token' => $user->createToken('ApiToken')->plainTextToken,

                    'type' => 'bearer',
                    
                ]
            ]);
        }

        return response()->json([

            'message' => 'Invalid credentials',

        ]);
    }
    
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message'=>'logged out'
        ]);
    }
}   
