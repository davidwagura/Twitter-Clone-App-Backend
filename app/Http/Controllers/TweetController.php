<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tweet;
use App\Models\Comment;
use App\Models\Follower;
use App\Models\Following;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $user->password = Hash::make($request->password);

        $user->save();

        return response()->json([
            
            'message' => $user ? 'User created successfully' : 'Error creating user',
        
            'user' => $user

        ],200);
    }

    public function comment(Request $request)
    {
        $comment = new Comment;

        $comment->body = $request->body;

        $comment->user_id = $request->user_id;

        $comment->tweet_id = $request->tweet_id;

        $comment->save();

        return response()->json([
            
            'message' => $comment ? 'Comment created successfully' : 'Error creating comment',
            
            'comment' => $comment
        
        ],200);

    }
    public function showTweet($id) //get
    {
        $tweet = Tweet::findOrFail($id);

        return response()->json($tweet);
    }

    public function comments($id) //get
    {
        $comment = Comment::where('tweet_id', $id)->get();

        return response()->json([

            'comment' => $comment,
    
        ]);
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
    
        return response()->json([
            
            'message' => $tweet ? 'Tweet liked successfully' : 'Error liking tweet',

            'tweet' => $tweet
        
        ],200);
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
    
        return response()->json([
            
            'message' => $tweet ? 'Tweet unlike successfully' : 'Tweet unlike not successful',
        
            'tweet' => $tweet
        
        ],200);
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
    
        return response()->json([
            
            'message' => $tweet ? 'Retweet successful' : 'Retweet not successful',
        
            'tweet' => $tweet
        
        ],200);
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
    
        return response()->json([
                    
            'message' => $tweet ? 'Unretweet successful' : 'Unretweet not successful',
        
            'tweet' => $tweet

        ],200);
}

    public function deleteTweet($tweet_id) //get
    {
        $tweet = Tweet::findOrFail($tweet_id);

        $count = $tweet->comments()->count(); 

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

        $user->password = Hash::make($request->password);

        $user->save();
    
        return response()->json(['message' => 'Password reset successfully'],200);
    }

    public function login(Request $request)
    {
        $request->validate([

            'email' => 'required',

            'password' => 'required',

        ]);

        $user = User::where('email', $request->email)->first();


        if(!empty($user)) {

            if($user->email === $request->email){
                
                $token  = $user->createToken("myToken")->plainTextToken;

                return response()->json([

                    'status' => true,

                    'message' => "login successful",

                    'token' => $token
                ],200);
            }

            return response()->json([

                'status' => false,

                'message' => "Email didn't match",

            ],401);
        }

        return response()->json([

            'message' => 'User not found.'
            
        ],404);

    }


    public function logout(Request $request)
    {
        if ($request->user()) {

            $request->user()->currentAccessToken()->delete();
    
            return response()->json([

                'message' => 'Logged out successfully'

            ], 200);

        } else {

            return response()->json([

                'error' => 'Unauthorized'

            ], 401);
        }
    }
        
    public function profile($user_id)
    {
        $user = User::findOrFail($user_id);
    
        $tweets = Tweet::where('user_id', $user->id)->get();
    
        return response()->json([

            'user' => $user,

            'tweets' => $tweets

        ], 200);
    }

    public function followers($follower_id, $user_to_follow_id)
    {
        $userToFollow = User::findOrFail($follower_id);
    
        $followersId = $userToFollow->followers_id;
    
        if(!empty($followersId)){

            $followersId = explode(',' , $followersId);

            if(!in_array($user_to_follow_id, $followersId)){

                $followersId[] = $user_to_follow_id;

                $userToFollow->followers_id = implode(',', $followersId);

                $userToFollow->followers++;
            }

        } else {

            $userToFollow->followers_id = $user_to_follow_id;

            $userToFollow->followers = 1;
        }
    
        $userToFollow->save();
    
        return response()->json([
            
            'message' => 'Followed successfully',

            'userToFollow' => $userToFollow
        
        ],200);
    }

    public function followersUnFollow($follower_id,$user_id)
    {
        $userToFollow = User::findOrFail($follower_id);
    
        $followersId = $userToFollow->followers_id;

    
        if (!empty($followersId)) {

            $explodedFollowersId = explode(',' , $followersId);
            
            $index = array_search(strval($user_id), $explodedFollowersId);
    
            if ($index !== false) {

                unset($explodedFollowersId[$index]);

                $userToFollow->followers_id = implode(',', $explodedFollowersId);

                $userToFollow->followers_id = max(0, $userToFollow->followers - 1);

            }
            if (empty($explodedFollowersId)) {

                $userToFollow->followers = 0;
            }

        } else {

            $userToFollow->followers = 0;
 
        }

        $userToFollow->save();
        
        return response()->json([
            
            'message' => 'Unfollow successful',

            'userToFollow' => $userToFollow
    
        ],200);
    }


    public function following($following_id, $user_to_follow_id)
    {
        $userToFollow = User::findOrFail($following_id);
    
        $followingId = $userToFollow->followings_id;
    
        if(!empty($followingId)){

            $followingId = explode(',' , $followingId);

            if(!in_array($user_to_follow_id, $followingId)){

                $followingId[] = $user_to_follow_id;

                $userToFollow->followings_id = implode(',', $followingId);

                $userToFollow->following++;
            }

        } else {

            $userToFollow->followings_id = $user_to_follow_id;

            $userToFollow->following = 1;
        }
        
        $userToFollow->save();
    
        return response()->json([

            'message' => 'Followed successfully',

            'user' => $userToFollow

        ],200);
    }


    public function connectionCount($myId)
    {
        $user = User::findOrFail($myId);
    
        $followers = explode(',', $user->followers_id);

        $following = explode(',', $user->followings_id);
    
        $followersCount = count(array_filter($followers, 'is_numeric'));

        $followingCount = count(array_filter($following, 'is_numeric'));
    
        return response()->json([

            'followers_count' => $followersCount,

            'following_count' => $followingCount

        ], 200);
    }

    public function showFollowers($myId)
    {    
        $user = User::findOrFail($myId);

        $followerIds = explode(',', $user->followers_id);
    
        $followers = [];

        foreach ($followerIds as $followerId) {

            $follower = User::find($followerId);

            if ($follower) {

                $followers[] = [

                    'id' => $follower->id,

                    'profile' => $follower

                ];
            }
        }
    
        return response()->json([

            'followers' => $followers
            
        ], 200);
    }
    
}
    

 


