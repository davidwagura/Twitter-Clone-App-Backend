<?php

namespace App\Http\Controllers;

use Alert;
use App\Models\User;
use App\Models\Tweet;
use App\Models\Comment;
use App\Models\Mention;
use App\Models\Message;
use App\Models\Profile;
use App\Models\Follower;
use App\Models\Following;
use App\Models\Notification;
use Illuminate\Http\Request;
use Ramsey\Collection\Collection;
use function Laravel\Prompts\alert;

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

        if ($tweet) {

            $this->tweetMention($tweet, $request->receiver_id);

            return response()->json([

                'message' => $tweet ? 'Tweet created successfully' : 'Validation failed',

                'tweet' => $tweet

            ],200);
        }

    }

    public function tweetMention(Tweet $tweet,$receiverId)
    {
        $user = $tweet->user;
    
        $mention = new Notification;
    
        $mention->body = $user->first_name . ' ' . $user->last_name . ' tagged you in a tweet.';
    
        $mention->createdBy = $receiverId;
    
        $mention->related_item_id = $tweet->id;
    
        $mention->user_id = $user->id;
    
        $mention->action_type = 'tweet';
    
        $mention->seen = false;
    
        $mention->save();

        return response()->json([

            'mention' => $mention,

            'message' => $mention ? 'Mention created successfully' : 'Failed to create mention'

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

        if ($comment->save()) {

            $this->commentMention($request->user_id, $request->receiver_id, $request->tweet_id); 

            return response()->json([
                
                'message' => $comment ? 'Comment created successfully' : 'Error creating comment',
                
                'comment' => $comment
            
            ],200);
        }
    }

    public function commentMention($userId, $receiverId, $tweetId)
    {
        $user = User::findOrFail($userId);

        $mention = new Notification;

        $mention->body = $user->first_name . ' ' . $user->last_name . ' mentioned you in a comment.';

        $mention->createdBy = $receiverId;

        $mention->related_item_id = $tweetId;

        $mention->user_id = $userId;

        $mention->action_type = 'comment';
        
        $mention->seen = false;

        $mention->save();

        return response()->json([

            'mention' => $mention,

            'message' => !$mention->isEmpty() ? 'Mention created successfully' : 'Failed to create mention'

        ],200);


        $user = User::findOrFail($userId);

        $notifications = new Notification;

        $notifications->body = $user->first_name .' ' . $user->last_name . ' commented on your tweet';

        $notifications->related_item_id = $tweetId;

        $notifications->user_id = $userId;

        $notifications->action_type = 'comment';
        
        $notifications->seen = false;

        $notifications->save();

        return response()->json([

            'notification' => $notifications,

            'message' => !$notifications->isEmpty() ? 'Notification created successfully' : 'Failed to create notification'

        ],200);

    }

    public function showTweet($user_id) //get
    {
        $tweets = Tweet::where('user_id', $user_id)->get();
    
        return response()->json([

            'tweets' => $tweets,

            'message' => !$tweets->isEmpty() ? 'Tweets displayed successfully' : 'Tweet  not displayed'

        ], 200);
    }
    

    public function comments($id) //get
    {
        $comment = Comment::where('tweet_id', $id)->get();

        return response()->json([

            'comment' => $comment,

            'message' => !$comment->isEmpty() ? 'Comments displayed successfully' : 'Comments failed to be displayed'
    
        ],200);
    }

    public function userTweets($id) //get
    {
        $comments = Comment::where('tweet_id', $id)->with('tweet')->get();

        return response()->json([

            'Tweets' => $comments,

            'message' => !$comments->isEmpty() ? 'Tweets displayed successfully' : 'No tweets found'

        ],200);
    }


    public function tweetComments($id) //get
    {
        $tweet = Tweet::with('comment')->findOrFail($id);

        return response()->json([

            'tweet' => $tweet,

            'message' => !$tweet->isEmpty() ? 'Comments displayed successfully' : 'No comments found'

        ],200);
    }


    public function likeTweet($tweet_id, $user_id)
    {
        $tweet = Tweet::findOrFail($tweet_id);

        $user = User::findOrFail($user_id);
    
        $likesId = $tweet->likes_id;
    
        if (!empty($likesId)) {

            $likesId = explode(',', $likesId);

            if (!in_array($user_id, $likesId)) {

                $likesId[] = $user_id;

                $tweet->likes_id = implode(',', $likesId);

                $tweet->likes++;
        
            }

        } 
        else {

            $tweet->likes_id = $user_id;

            $tweet->likes = 1;
        }

        $tweet->save();

        if($tweet->save())
        {

            $this->likeNotification($tweet_id,$user_id);

            return response()->json([
                
                'message' => $tweet ? 'Tweet liked successfully' : 'Error liking tweet',

                'tweet' => $tweet
            
            ],200);
        }
    }
    
    public function likeNotification($tweet_id,$user_id)
    {
        $user = User::findOrFail($user_id);

        $notifications = new Notification;

        $notifications->body = $user->first_name .' ' . $user->last_name . ' liked your tweet';

        $notifications->related_item_id = $tweet_id;

        $notifications->user_id = $user_id;

        $notifications->action_type = 'like';
            
        $notifications->seen = false;

        $notifications->save();   
        
        return response()->json([

            'notification' => $notifications,

            'message' => !$notifications->isEmpty() ? 'Notification created successfully' : 'No notification found'

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

        $user = User::findOrFail($user_id);
    
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

        if($tweet->save())
        {
            $this->retweetNotification($user_id,$tweet_id);

            return response()->json([
                
                'message' => $tweet ? 'Retweet successful' : 'Retweet not successful',
            
                'tweet' => $tweet
            
            ],200);
        }
    }

    public function retweetNotification($user_id,$tweet_id)
    {
        $user = User::findOrFail($user_id);

        $notifications = new Notification;

        $notifications->body = $user->first_name .' ' . $user->last_name . ' retweeted your tweet';

        $notifications->related_item_id = $tweet_id;

        $notifications->user_id = $user_id;

        $notifications->action_type = 'retweet';
                
        $notifications->seen = false;

        $notifications->save();

        return response()->json([

            'message' => !$notifications->isEmpty() ? 'Notification created successfully' : 'Notification data is empty',

            'notification' => $notifications

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

            return response()->json([

                'tweet' => $tweet,
            
                'message' => $tweet ? 'Tweet deleted successfully' : 'Failed to delete tweet'

            ],200);

        }

    }

    public function resetPassword(Request $request, $user_id)
    {
        $request->validate([

            'password' => 'required',

            'new_password' => 'required'

        ]);
    
        $user = User::findOrFail($user_id);

        $old_password = $user->password;

        if($old_password === $request->password)
        {

            $old_password === $request->new_password;

            if ($user->save())
            {
                $this->resetPasswordNotification($user_id);
        
                return response()->json([
                        
                    'message' => $user ? 'Password reset successfully' : 'Password reset failed',
        
                    'user' => $user
                    
                ],200);
            }
        }

    }

    public function resetPasswordNotification($user_id)
    {
        $user = User::findOrFail($user_id);

        $notifications = new Notification;

        $notifications->body = $user->first_name .' ' . $user->last_name . ' your password have been successfully reset';

        $notifications->related_item_id = $user_id;

        $notifications->user_id = $user_id;

        $notifications->action_type = 'password reset';
        
        $notifications->seen = false;

        $notifications->save();

        return response()->json([

            'notification' => $notifications,

            'message' => !$notifications->isEmpty() ? 'Notification created successfully' : 'Notification data is Empty'

        ],200);

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

            'tweets' => $tweets,

            'message' => 'Profile displayed successfully'

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

        if($userToFollow->save()){

            $this->followerNotification($follower_id, $user_to_follow_id);
   
            return response()->json([
                
                'message' => $userToFollow ? 'Followed successfully' : 'Failed to follow the user',

                'userToFollow' => $userToFollow
            
            ],200);
        }
    }

    public function followerNotification($follower_id,$user_to_follow_id)
    {
        $user = User::findOrFail($follower_id);

        $notifications = new Notification;
        
        $notifications->body = $user->first_name .' ' . $user->last_name . ' started following you';

        $notifications->related_item_id = $user_to_follow_id;

        $notifications->user_id = $follower_id;

        $notifications->action_type = 'follower';
        
        $notifications->seen = false;

        $notifications->save();

        return response()->json([

            'notification' => $notifications,

            'message' => !$notifications->isEmpty() ? 'Notification created successfully' : 'Notification data is empty'

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
            
            'message' => $userToFollow ? 'Unfollow successful' : 'Unfollow not successful',

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

            'message' => $userToFollow ? 'Followed successfully' : 'Failed to follow user',

            'user' => $userToFollow

        ],200);
    }

    public function followingUnfollow($following_id, $user_id)
    {
        $userUnFollow = User::findOrFail($following_id);
        
        $followingId = $userUnFollow->followings_id;
    
        if (!empty($followingId)) {

            $explodedFollowingId = explode(',', $followingId);
            
            $index = array_search(strval($user_id), $explodedFollowingId);
    
            if ($index !== false) {

                unset($explodedFollowingId[$index]);
    
                $userUnFollow->followings_id = implode(',', $explodedFollowingId);

                $userUnFollow->following = count($explodedFollowingId);
            }
        } else {

            $userUnFollow->following = 0;

        }
    
        $userUnFollow->save();
        
        return response()->json([

            'message' => $userUnFollow ? 'Unfollow successful' : 'Unfollow unsuccessful',

            'userToFollow' => $userUnFollow

        ], 200);
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
    
    public function showFollowing($myId)
    {
        $user = User::findOrFail($myId);

        $followingIds = explode(',', $user->followings_id);

        $followings = [];

        foreach ($followingIds as $followingId)
        {
            $following = User::find($followingId);

            if ($following) {
                
                $followings[] = [

                    'id' => $following->id,

                    'profile' => $following
                ];
            }
        }

        return response()->json([

            'followings' => $followings,

            'massage' => $following ? 'Displayed successfully' : 'Failed to display'

        ],200);
    }

    public function getUserLikedTweets($user_id)
    {
        $tweet = Tweet::findOrFail($user_id);
    
        $likedTweetIds = explode(',', $tweet->likes_id);
    
        $likedTweets = User::whereIn('id', $likedTweetIds)->get();
    
        return response()->json([

            'message' => $tweet ? 'User liked tweets got successfully' : 'Failed to get user liked tweets',

            'liked_tweets' => $likedTweets,

        ], 200);
    }

    public function getUserTweetComments($user_id)
    {
        $user = User::findOrFail($user_id);
    
        $comment = $user->comment()->with('tweet')->get();
        
        return response()->json([

            'comments' => $comment,

            'message' => $comment ? 'User comments on tweets retrieved successfully' : 'No comments found for the user on tweets'

        ], 200);
    }


    public function showHighlights()
    {

    }

    public function showArticles()
    {

    }

    public function showMedia()
    {

    }

    public function getMentions($createdBy, $user_id)
    {
        $mentions = Notification::where('createdBy', $createdBy)

                                ->with('user')

                                ->orderBy('created_at', 'desc')

                                ->get();

        $user =  Notification::where('user_id', $user_id)->get();
        
        return response()->json([

            'user' => $user,

            'mentions' => $mentions,

            'message' => $mentions->isNotEmpty() ? 'Mentions displayed successfully' : 'No mentions found'

        ],200);
    }
    

    public function getNotifications($user_id)
    {
        $user = User::where('id' ,$user_id)->with(['notifications' => function ($query) {

            $query->orderBy('created_at', 'desc');

        }])->get();

        return response()->json([

            'notification' => $user,

            'message' => $user ? 'Notifications displayed successfully' : 'Failed to display notifications'
        
        ], 200);
    }

    public function messages(Request $request, $sender_id, $receiver_id)
    {
    
        $message = new Message;
                
        $message->body = $request->body;
    
        $message->sender_id = $sender_id;

        $message->receivers_id = $receiver_id;
    
        $message->save();
        
        return response()->json([

            'message' => $message ? 'Message sent successfully' : 'Error sending message',

            'data' => $message

        ],200);
    }

    public function deleteOneMessage($message_id)
    {
        $message = Message::findOrFail($message_id);

        $message->delete();

        return response()->json([

            'message' => $message ? 'Message deleted successfully' : 'Failed to delete message',

            'data' => $message

        ],200);
    }

    public function deleteConversation($sender_id, $receivers_id)
    {
        $messagesToDelete = Message::where('sender_id', $sender_id)

                                    ->where('receivers_id', $receivers_id)

                                    ->orWhere('sender_id', $receivers_id)

                                    ->get();
    
        foreach ($messagesToDelete as $message) {

            $message->delete();

        }
    
        return response()->json([

            'message' => $messagesToDelete ? 'All messages between sender and receiver have been deleted' : 'No Messages to be deleted',

            'deleted_messages' => $messagesToDelete

        ], 200);
    }
    
    public function followingTweets($user_id)
    {
       $user = User::where('id', $user_id)->pluck('followings_id', 'id');

        $tweet = Tweet::where('user_id', $user)->get();

        return response()->json([

            'message' => $tweet ? 'Displaying tweets' :  'Failed to display tweets',

            'tweet' => $tweet
        ],200);

    }

    public function tweetsForYou()
    {

        $users = User::with(['tweet' => function ($query) {

            $query->orderBy('created_at', 'desc');

        }])->get();
            

        return response()->json([

            'user' => $users,

            'message' => $users ? 'Tweets displayed successfully' : 'Failed to load tweets',
        ]);
    }

    public function trends()
    {
        //withCount-> count the number of comments associated with each tweet

        $tweetWithMostComments = Tweet::withCount('comments')

            ->orderByDesc('comments_count')

            ->take(10)->get();

        return response()->json([

            'trending' => $tweetWithMostComments,

            'message' => !$tweetWithMostComments->isEmpty() ? 'Trending tweets displayed successfully' : 'No comments found',

        ], 200);
    }

    public function createProfile(Request $request)
    {
        $profile = new Profile;

        $profile->user_id = $request->user_id;

        $profile->name = $request->name;

        $profile->bio = $request->bio;

        $profile->location = $request->location;

        $profile->website = $request->website;

        $profile->birth_date = $request->birth_date;

        $profile->save();

        return response()->json([

            'profile' => $profile,

            'message' => $profile ? 'Profile created successfully' : 'Error creating profile',

        ],200);
        
    }

    public function editProfile(Request $request , $user_id)
    {
        $record = Profile::where('user_id', $user_id)->first();

        $record->name = $request->name;

        $record->bio = $request->bio;
    
        $record->location = $request->location;
    
        $record->website = $request->website;
    
        $record->birth_date = $request->birth_date;

        $record->save();
    
        return response()->json([

            'userProfile' => $record,

            'message' => !$record->isEmpty() ? 'Profile updated successfully' : 'Profile not found'

        ],200);
    }

    public function userComments($user_id)
    {

        $comments = Comment::where('user_id', $user_id)
                            
                            ->with('tweet')
                            
                            ->latest()
                            
                            ->get();

        return response()->json([

            'comment' => $comments,

            'message' => !$comments->isEmpty() ? 'User comments displayed successfully' : 'No user comment found'
        ]);
    }

    public function userConversations($sender_id, $receiver_id)
    {
        $conversations = Message::where('receivers_id', $receiver_id)

                                ->where('sender_id', $sender_id)

                                ->latest()
                                
                                ->get();

        return response()->json([

            'conversations' => $conversations,

            'message' => !$conversations->isEmpty() ? 'Conversation displayed successfully' : 'Empty conversation'
        ]);
    }
}
    

 


