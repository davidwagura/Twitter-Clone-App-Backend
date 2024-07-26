<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Tweet;
use App\Models\GroupUser;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Profile;
use App\Models\Conversation;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\commentComment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TweetController extends Controller
{

    public function home()
    {

        $tweets =  Tweet::all();

        return response()->json($tweets);
    }

    public function tweet(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [

            'body' => 'nullable|string',

            'user_id' => ['required', 'integer'],

            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);

        if ($validator->fails()) {

            return response()->json([

                'status' => false,

                'message' => 'Validation failed',

                'errors' => $validator->errors(),

            ], 400);
        }

        // Prepare tweet data
        $tweetData = [

            'body' => $request->body,

            'user_id' => $request->user_id,

        ];

        // Handle image upload if present
        if ($request->hasFile('image_path')) {

            $image = $request->file('image_path');

            $originalPath = $image->store('images/tweet', 'public');

            $tweetData['image_path'] = $originalPath;
        }

        // Create the tweet
        $tweet = Tweet::create($tweetData);

        return response()->json([

            'status' => true,

            'message' => 'Tweet created successfully',

            'data' => $tweet,

        ], 201);
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

        ], 200);
    }

    public function comment(Request $request)
    {
        $comment = new Comment;

        $comment->body = $request->body;

        $comment->tweet_id = $request->tweet_id;

        $comment->user_id = $request->user_id;

        $comment->save();

        if ($comment->save()) {

            // $this->commentMention($request->user_id, $request->tweet_id);

            return response()->json([

                'message' => 'Comment created successfully',

                'comment' => $comment

            ], 200);
        }

        return response()->json([

            'message' => 'Error creating comment'

        ], 500);
    }

    public function commentMention($userId, $tweetId)
    {
        $user = User::findOrFail($userId);

        $mention = new Notification;

        $mention->body = $user->first_name . ' ' . $user->last_name . ' mentioned you in a comment.';

        // $mention->createdBy = $userId;

        $mention->related_item_id = $tweetId;

        $mention->user_id = $userId;

        $mention->action_type = 'comment';

        $mention->seen = false;

        $mention->save();

        return response()->json([

            'mention' => $mention,

            'message' => $mention ? 'Mention created successfully' : 'Failed to create mention'

        ], 200);


        $user = User::findOrFail($userId);

        $notifications = new Notification;

        $notifications->body = $user->first_name . ' ' . $user->last_name . ' commented on your tweet';

        $notifications->related_item_id = $tweetId;

        $notifications->user_id = $userId;

        $notifications->action_type = 'comment';

        $notifications->seen = false;

        $notifications->save();

        return response()->json([

            'notification' => $notifications,

            'message' => !$notifications->isEmpty() ? 'Notification created successfully' : 'Failed to create notification'

        ], 200);
    }

    public function showTweet($id) //get
    {
        $tweet = Tweet::where('id', $id)->with('user')->with('comments')->first();


        return response()->json([

            'tweet' => $tweet,

            'message' => $tweet ? 'Tweets displayed successfully' : 'Tweet  not displayed'

        ], 200);
    }

    public function userTweets($userId)
    {
        $tweet = Tweet::where('user_id', $userId)->with('user')->latest()->get();

        return response()->json([

            'tweet' => $tweet,

            'message' => $tweet ? 'Tweet displayed successfully' : 'No tweets found'

        ], 200);
    }


    public function getUser($id)
    {
        $user = User::where('id', $id)->get();

        return response()->json([

            'user' => $user,

            'message' => $user ? 'User displayed successfully' : 'User not found'

        ]);
    }

    public function getAllUsers() 
    {
        $users = User::all();

        return response()->json([

            'message' => $users->isNotEmpty() ? 'Users displayed successfully' : 'Error displaying tweets',

            'users' => $users

        ]);

    }

    //Tweet comments

    public function comments($tweet_id) //get
    {
        $comment = Comment::where('tweet_id', $tweet_id)

            ->with('user')

            ->with('commentComment')

            ->latest()

            ->get();

        return response()->json([

            'comment' => $comment,

            'message' => $comment->isNotEmpty() ? 'Comments displayed successfully' : 'Comments failed to be displayed'

        ], 200);
    }

    public function getComment($id) //get
    {
        $comment = comment::where('id', $id)->with('user')->first();

        return response()->json([

            'message' => $comment->isNotEmpty() ? 'Comment displayed successfully' : 'Comment not found',

            'Comment' => $comment

        ], 200);
    }

    public function likeTweet($tweet_id, $user_id)
    {
        $tweet = Tweet::findOrFail($tweet_id);

        $tweetOwner = $tweet->user;

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

        if ($tweet->save()) {

            $this->likeNotification($tweet_id, $user_id, $tweetOwner->id);

            return response()->json([

                'message' => $tweet ? 'Tweet liked successfully' : 'Error liking tweet',

                'tweet' => $tweet

            ], 200);
        }
    }

    public function likeNotification($tweet_id, $user_id, $tweetOwner)
    {
        $liker = User::findOrFail($user_id);

        $notification = new Notification;

        $notification->body = $liker->first_name . ' ' . $liker->last_name . ' liked your tweet';

        $notification->related_item_id = $tweet_id;

        $notification->user_id = $tweetOwner;

        $notification->createdBy = $tweetOwner;

        $notification->action_type = 'like';

        $notification->seen = false;

        $notification->save();

        return response()->json([

            'notification' => $notification,

            'message' => $notification ? 'Notification created successfully' : 'No notification found'

        ], 200);
    }

    public function likeComment($comment_id, $user_id)
    {
        $comment = Comment::findOrFail($comment_id);

        $commentOwner = $comment->user;

        $likesId = $comment->likes_id;

        if (!empty($likesId)) {

            $likesId = explode(',', $likesId);

            if (!in_array($user_id, $likesId)) {

                $likesId[] = $user_id;

                $comment->likes_id = implode(',', $likesId);

                $comment->likes++;
            }
        } else {

            $comment->likes_id = $user_id;

            $comment->likes = 1;
        }

        $comment->save();

        $this->commentLikeNotification($commentOwner->id, $comment_id, $user_id); // Pass the user_id, not the user object

        return response()->json([

            'message' => $comment ? 'Comment liked successfully' : 'Error liking tweet',

            'comment' => $comment

        ], 200);
    }

    public function commentLikeNotification($commentOwner_id, $comment_id, $user_id)
    {
        $user = User::findOrFail($user_id);

        $notification = new Notification;

        $notification->body = $user->first_name . ' ' . $user->last_name . ' liked your comment';

        $notification->related_item_id = $comment_id;

        $notification->createdBy =  $user_id;

        $notification->user_id = $commentOwner_id;

        $notification->action_type = 'like';

        $notification->seen = false;

        $notification->save();

        return response()->json([

            'notification' => $notification,

            'message' => $notification ? 'Notification created successfully' : 'Failed to create notification'

        ], 200);
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

            'message' => $tweet ? 'Tweet unlike successful' : 'Tweet unlike not successful',

            'tweet' => $tweet

        ], 200);
    }

    public function unlikeComment($comment_id, $user_id)
    {
        $comment = Comment::findOrFail($comment_id);

        $likesId = $comment->likes_id;

        if (!empty($likesId)) {

            $explodedLikesId = explode(',', $likesId);

            $index = array_search(strval($user_id), $explodedLikesId);

            if ($index !== false) {

                unset($explodedLikesId[$index]);

                $comment->likes_id = implode(',', $explodedLikesId);

                $comment->likes = max(0, $comment->likes - 1);
            }
            // If likes array is empty, set likes to zero
            if (empty($explodedLikesId)) {

                $comment->likes = 0;
            }
        } else {

            $comment->likes = 0;
        }

        $comment->save();

        return response()->json([

            'message' => $comment ? 'Comment unlike successful' : 'Tweet unlike not successful',

            'comment' => $comment

        ], 200);
    }



    public function retweet($tweet_id, $user_id)
    {
        $tweet = Tweet::findOrFail($tweet_id);

        $tweetOwner = $tweet->user_id;

        $retweetsId = $tweet->retweets_id;

        if (!empty($retweetsId)) {

            $retweetsId = explode(',', $retweetsId);

            if (!in_array($user_id, $retweetsId)) {

                $retweetsId[] = $user_id;

                $tweet->retweets_id = implode(',', $retweetsId);

                $tweet->retweets++;
            }
        } else {

            $tweet->retweets_id = $user_id;

            $tweet->retweets = 1;
        }

        $tweet->save();

        if ($tweet->save()) {

            $this->retweetNotification($tweetOwner, $user_id, $tweet_id);

            return response()->json([

                'message' => 'Retweet successful',

                'tweet' => $tweet

            ], 200);
        } else {

            return response()->json([

                'message' => 'Retweet not successful',

            ], 400);
        }
    }

    public function retweetNotification($tweetOwner, $user_id, $tweet_id)
    {
        $user = User::findOrFail($user_id);

        $notification = new Notification;

        $notification->body = $user->first_name . ' ' . $user->last_name . ' retweeted your tweet';

        $notification->related_item_id = $tweet_id;

        $notification->createdBy =  $user_id;

        $notification->user_id = $tweetOwner;

        $notification->action_type = 'retweet';

        $notification->seen = false;

        $notification->save();

        return response()->json([

            'notification' => $notification,

            'message' => $notification ? 'Notification created successfully' : 'Notification creation failed',

        ], 200);

    }

    public function retweetComment($comment_id, $user_id)
    {
        $comment = Comment::findOrFail($comment_id);

        $commentOwner = $comment->user;

        $retweetsId = $comment->retweets_id;

        if (!empty($retweetsId)) {

            $retweetsId = explode(',', $retweetsId);

            if (!in_array($user_id, $retweetsId)) {

                $retweetsId[] = $user_id;

                $comment->retweets_id = implode(',', $retweetsId);

                $comment->retweets++;
            }

        } else {

            $comment->retweets_id = $user_id;

            $comment->retweets = 1;
        }

        $comment->save();

        if ($comment->save()) {

            $this->retweetCommentNotification($commentOwner->id, $user_id, $comment_id);

            return response()->json([

                'message' => $comment ? 'Retweet successful' : 'Retweet not successful',

                'comment' => $comment

            ], 200);

        }

    }

    public function retweetCommentNotification($commentOwner, $user_id, $comment_id)
    {
        $user = User::findOrFail($user_id);

        $notifications = new Notification;

        $notifications->body = $user->first_name . ' ' . $user->last_name . ' retweeted your tweet';

        $notifications->related_item_id = $comment_id;

        $notifications->createdBy =  $user_id;

        $notifications->user_id = $commentOwner;

        $notifications->action_type = 'retweet';

        $notifications->seen = false;

        $notifications->save();

        return response()->json([

            'notification' => $notifications,

            'message' => $notifications ? 'Notification created successfully' : 'Notification data is empty',

        ], 200);

    }


    public function unretweet($tweet_id, $user_id)
    {
        $tweet = Tweet::findOrFail($tweet_id);

        $retweetsId = $tweet->retweets_id;

        if (!empty($retweetsId)) {

            $explodedRetweetsId = explode(',', $retweetsId);

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

        ], 200);

    }

    public function unretweetComment($comment_id, $user_id)
    {
        $comment = Comment::findOrFail($comment_id);

        $retweetsId = $comment->retweets_id;

        if (!empty($retweetsId)) {

            $explodedRetweetsId = explode(',', $retweetsId);

            $index = array_search(strval($user_id), $explodedRetweetsId);

            if ($index !== false) {

                unset($explodedRetweetsId[$index]);

                $comment->retweets_id = implode(',', $explodedRetweetsId);

                $comment->retweets = max(0, count($explodedRetweetsId));
            }

        }

        $comment->save();

        return response()->json([

            'message' => $comment ? 'Unretweet successful' : 'Unretweet not successful',

            'comment' => $comment

        ], 200);

    }

    public function addCommentComments(Request $request)
    {
        $comment = new commentComment;

        $comment->body = $request->body;

        $comment->comment_id = $request->comment_id;

        $comment->user_id = $request->user_id;

        $comment->save();

        return response()->json([

            'message' => $comment ? 'Request successful' : 'Request failed',

            'comment' => $comment

        ], 200);

    }

    public function getCommentComments($comment_id)
    {
        $comment = commentComment::where('comment_id', $comment_id)

            ->with('user')

            ->latest()

            ->get();

        return response()->json([

            'message' => $comment ? 'Request successful' : 'Request failed',

            'comment' => $comment

        ], 200);

    }


    public function deleteTweet($tweet_id) //get
    {
        $tweet = Tweet::findOrFail($tweet_id);

        $count = $tweet->comments()->count();

        if ($count > 1) {

            return response()->json(['message' => 'You can not delete the tweet with comments'], 403);

        } else {

            $tweet->comments()->delete();

            $tweet->delete();

            return response()->json([

                'tweet' => $tweet,

                'message' => $tweet ? 'Tweet deleted successfully' : 'Failed to delete tweet'

            ], 200);

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

        if ($old_password === $request->password) {

            $old_password === $request->new_password;

            if ($user->save()) {
                $this->resetPasswordNotification($user_id);

                return response()->json([

                    'message' => $user ? 'Password reset successfully' : 'Password reset failed',

                    'user' => $user->password = $request->new_password

                ], 200);

            }

        }

    }

    public function resetPasswordNotification($user_id)
    {
        $user = User::findOrFail($user_id);

        $notifications = new Notification;

        $notifications->body = $user->first_name . ' ' . $user->last_name . ' your password have been successfully reset';

        $notifications->related_item_id = $user_id;

        $notifications->user_id = $user_id;

        $notifications->action_type = 'password reset';

        $notifications->seen = false;

        $notifications->save();

        return response()->json([

            'notification' => $notifications,

            'message' => $notifications ? 'Notification created successfully' : 'Failed to create notification'

        ], 200);

    }



    public function login(Request $request)
    {
        $request->validate([

            'email' => 'required',

            'password' => 'required',

        ]);

        $user = User::where('email', $request->email)->first();


        if (!empty($user)) {

            if ($user->email === $request->email) {

                $token  = $user->createToken("myToken")->plainTextToken;

                return response()->json([

                    'status' => true,

                    'message' => "Login successful",

                    'token' => $token,

                    'user' => $user

                ], 200);

            }

            return response()->json([

                'status' => false,

                'message' => "Email didn't match",

            ], 401);
            
        }

        return response()->json([

            'message' => 'User not found.'

        ], 404);

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

        // {
        //     auth()->user()->tokens()->delete();
        //     return response()->json([
        //         'status' => true,
        //         'message' => 'User logged out'
        //     ]);
        // }
    }

    public function profile($user_id)
    {
        $tweets = User::where('id', $user_id)->with('tweet')->get();

        return response()->json([

            'tweets' => $tweets,

            'message' => 'Profile displayed successfully'

        ], 200);

    }

    public function followers($follower_id, $user_to_follow_id)
    {
        $userToFollow = User::findOrFail($follower_id);

        $owner = $userToFollow;

        $followersId = $userToFollow->followers_id;

        if (!empty($followersId)) {

            $followersId = explode(',', $followersId);

            if (!in_array($user_to_follow_id, $followersId)) {

                $followersId[] = $user_to_follow_id;

                $userToFollow->followers_id = implode(',', $followersId);

                $userToFollow->followers++;
            }

        } else {

            $userToFollow->followers_id = $user_to_follow_id;

            $userToFollow->followers = 1;
        }

        $userToFollow->save();

        if ($userToFollow->save()) {

            $this->followerNotification($follower_id, $user_to_follow_id, $owner->id);

            return response()->json([

                'message' => $userToFollow ? 'Followed successfully' : 'Failed to follow the user',

                'userToFollow' => $userToFollow

            ], 200);

        }

    }

    public function followerNotification($follower_id, $user_to_follow_id, $owner)
    {
        $user = User::findOrFail($follower_id);

        $notifications = new Notification;

        $notifications->body = $user->first_name . ' ' . $user->last_name . ' started following you';

        $notifications->related_item_id = $user_to_follow_id;

        $notifications->createdBy =  $follower_id;

        $notifications->user_id = $owner;

        $notifications->action_type = 'follower';

        $notifications->seen = false;

        $notifications->save();

        return response()->json([

            'notification' => $notifications,

            'message' => $notifications ? 'Notification created successfully' : 'Failed to create notification'

        ], 200);

    }

    public function followersUnFollow($follower_id, $user_id)
    {
        $userToFollow = User::findOrFail($follower_id);

        $followersId = $userToFollow->followers_id;


        if (!empty($followersId)) {

            $explodedFollowersId = explode(',', $followersId);

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

        ], 200);

    }


    public function following($user_id, $user_to_follow_id)
    {
        $userToFollow = User::findOrFail($user_id);

        $followingId = $userToFollow->followings_id;

        if (!empty($followingId)) {

            $followingId = explode(',', $followingId);

            if (!in_array($user_to_follow_id, $followingId)) {

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

        ], 200);

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

            'message' => !$follower->isEmpty() ? 'Followers displayed successfully' : 'No followers found',

            'followers' => $followers

        ], 200);

    }

    public function showFollowing($myId)
    {
        $user = User::findOrFail($myId);

        $followingIds = explode(',', $user->followings_id);

        $followings = [];

        foreach ($followingIds as $followingId) {
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

            'message' => $following ? 'Displayed successfully' : 'Failed to display'

        ], 200);

    }

    public function getUserLikedTweets($user_id)
    {
        // Retrieve all tweets where the likes_id column contains the user_id
        $likedTweets = Tweet::whereRaw("FIND_IN_SET(?, likes_id)", [$user_id])->with('user')->get();

        return response()->json([

            'message' => $likedTweets->isNotEmpty() ? 'User liked tweets retrieved successfully' : 'No liked tweets found',

            'liked_tweets' => $likedTweets,

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

            ->where('user_id', $user_id)

            ->with('user')

            ->orderBy('created_at', 'desc')

            ->get();

        return response()->json([

            'mentions' => $mentions,

            'message' => $mentions->isNotEmpty() ? 'Mentions displayed successfully' : 'No mentions found'

        ], 200);
    }


    public function getNotifications($user_id)
    {
        $notifications = Notification::where('user_id', $user_id)

            ->orderBy('created_at', 'desc')

            ->with('user')

            ->get();

        $notifications->transform(function ($notification) {

            if ($notification->action_type == 'follower') {

                $relatedUser = User::find($notification->related_item_id);

                $notification->related_item = $relatedUser;
            } else {

                $relatedTweet = Tweet::find($notification->related_item_id);

                $notification->related_item = $relatedTweet;
            }

            return $notification;
        });

        return response()->json([

            'notifications' => $notifications,

            'message' => $notifications->isNotEmpty() ? 'Notifications displayed successfully' : 'Failed to display notifications'

        ], 200);
    }


    public function messages(Request $request, $sender_id, $receivers_id)
    {
        $request->validate([

            'body' => 'nullable|string',

            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        $message = new Message;

        $message->body = $request->body;

        $message->sender_id = $sender_id;

        $message->receivers_id = $receivers_id;

        if ($request->hasFile('image_path')) {

            $image = $request->file('image_path');

            $imagePath = $image->store('images/messages', 'public');

            $message->image_path = $imagePath;
        }

        $message->save();

        return response()->json([

            'message' => $message ? 'Message sent successfully' : 'Error sending message',

            'data' => $message

        ], 200);
    }

    public function deleteOneMessage($message_id)
    {
        $message = Message::findOrFail($message_id);

        $message->delete();

        return response()->json([

            'message' => $message ? 'Message deleted successfully' : 'Failed to delete message',

            'data' => $message

        ], 200);
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
        $user = User::find($user_id);

        if (!$user) {

            return response()->json([

                'message' => 'User not found',

            ], 404);
        }

        $followings_ids = explode(',', $user->followings_id);

        $tweets = Tweet::whereIn('user_id', $followings_ids)->with('user')->with('comments')->get();

        // $tweets = $tweets->map(function ($tweet) use ($user_id) {

        //     $tweet->isLiked = $tweet->isLikedByUser($user_id);

        //     $tweet->isRetweeted = $tweet->isRetweetedByUser($user_id);

        //     return $tweet;
        // });


        return response()->json([

            'message' => $tweets->isEmpty() ? 'No tweets found' : 'Displaying tweets',

            'tweets' => $tweets,

        ], 200);
    }

    public function tweetsForYou(Request $request)
    {
        $tweets = Tweet::with('user', 'comments')->latest()->get();

        // $tweets = $tweets->map(function ($tweet) use ($userId) {

        //     $tweet->isLiked = $tweet->isLikedByUser($userId);

        //     $tweet->isRetweeted = $tweet->isRetweetedByUser($userId);

        //     return $tweet;
        // });

        return response()->json([

            'tweets' => $tweets,

            'message' => $tweets ? 'Tweets displayed successfully' : 'Failed to load tweets',

        ], 200);
    }

    public function trends()
    {

        $count = Tweet::withCount('comments')->orderByDesc('comments_count')->with('user')->take(10)->get();

        return response()->json([

            'trending' => $count,

            'message' => !$count->isEmpty() ? 'Trending tweets displayed successfully' : 'No comments found',

        ], 200);
    }

    public function createProfile(Request $request)
    {
        $profile = new Profile;
    
        $profile->user_id = $request->user_id;

        $profile->name = $request->name;
    
        if ($request->hasFile('profile_img')) {

            $profileImgPath = $request->file('profile_img')->store('images/profile', 'public');

            $profile->profile_img = $profileImgPath;

        }
    
        if ($request->hasFile('background_img')) {

            $backgroundImgPath = $request->file('background_img')->store('images/background', 'public');

            $profile->background_img = $backgroundImgPath;

        }

        $profile->bio = $request->bio;

        $profile->location = $request->location;

        $profile->website = $request->website;

        $profile->birth_date = $request->birth_date;
    
        $profile->save();
    
        return response()->json([

            'profile' => $profile,

            'message' => $profile ? 'Profile created successfully' : 'Error creating profile',

        ], 200);

    }
    
    public function editProfile(Request $request, $user_id)
    {

        try {

            $record = Profile::where('user_id', $user_id)->first();
        
            if (!$record) {

                return response()->json([

                    'message' => 'Profile not found'

                ], 404);

            }
    
            $record->name = $request->name;
    
            if ($request->hasFile('profile_img')) {

                if ($record->profile_img && Storage::disk('public')->exists($record->profile_img)) {

                    Storage::disk('public')->delete($record->profile_img);

                }
    
                $profileImgPath = $request->file('profile_img')->store('images/profile', 'public');

                $record->profile_img = $profileImgPath;
            }
    
            if ($request->hasFile('background_img')) { // Ensure the form field name matches here

                // Delete old background image if it exists
                if ($record->background_img && Storage::disk('public')->exists($record->background_img)) {

                    Storage::disk('public')->delete($record->background_img);

                }
    
                $backgroundImgPath = $request->file('background_img')->store('images/background', 'public'); // Ensure the form field name matches here

                $record->background_img = $backgroundImgPath;
            }
            
            $record->bio = $request->bio;

            $record->location = $request->location;

            $record->website = $request->website;

            $record->birth_date = $request->birth_date;
    
            $record->save();

            \Log::debug($record);
    
            return response()->json([

                'userProfile' => $record,

                'message' => 'Profile updated successfully',

            ], 200);

        } catch (\Exception $e) {

            // Log the error message
            \Log::error('Profile update error: '.$e->getMessage());
            
            return response()->json([

                'message' => 'Internal server error',

                'error' => $e->getMessage()

            ], 500);

        }
    }
    
    public function getProfile($user_id) {

        $profile = Profile::where('user_id', $user_id)->get();

        \Log::debug($profile);

        if(!$profile) {

            return response()->json([

                'message' => 'user not found'

            ],404);

        } else {

            return response()->json([

                'message' => 'Profile displayed successfully',

                'data' => $profile

            ],200) ;

        }

    }

    public function userComments($user_id)
    {

        $comments = Comment::where('user_id', $user_id)

            ->with('tweet')

            ->with('user')

            ->latest()

            ->get();

        return response()->json([

            'comment' => $comments,

            'message' => !$comments->isEmpty() ? 'User comments displayed successfully' : 'No user comment found'
        ]);
    }

    public function getMessages($sender_id, $receivers_id)
    {
        $message = Message::where('receivers_id', $receivers_id)

            ->where('sender_id', $sender_id)

            ->with('user')

            ->latest()

            ->get();

        return response()->json([

            'data' => $message,

            'message' => !$message->isEmpty() ? 'Conversation displayed successfully' : 'Empty conversation'

        ], 200);
    }

    public function conversation($sender_id)
    {

        $conversations = Message::where(function ($query) use ($sender_id) {

            $query->where('sender_id', $sender_id)

                ->orWhere('receivers_id', $sender_id);
        })

            ->oldest()

            ->get()

            ->groupBy(function ($item) use ($sender_id) {

                return $item->sender_id == $sender_id ? $item->receivers_id : $item->sender_id;
            });

        $cleanConversations = [];

        foreach ($conversations as  $key => $conversation) {

            $user = User::find($key);

            $cleanConversations[] = [

                'user' => $user,

                'conversation' => $conversation

            ];
        };

        return response()->json([

            'data' => $cleanConversations,

            'message' => $cleanConversations ? 'Conversation displayed successfully' : 'No comments found'

        ]);
    }

    public function createGroup(Request $request)
    {

        $request->validate([

            'name' => 'required|string|max:255',

            'image_path' => 'nullable|string|max:255',

            'creator_id' => 'required|exists:users,id',

        ]);

        $group = Group::create([

            'name' => $request->name,

            'image_path' => $request->image_path,

            'creator_id' => $request->creator_id,

        ]);

        return response()->json([
            
            'group' => $group,

            'message' => $group ? 'Group created successfully' : 'Error creating group'
        
        ], 200);

    }

    public function addMembers(Request $request, $groupId)
    {

        $request->validate([

            'user_id' => 'required',

        ]);

        $groupUser = GroupUser::create([

            'group_id' => $groupId,

            'user_id' => $request->user_id,

        ]);

        if($request->user_id) {

            $this->members($request->user_id, $groupId);

            return response()->json([

                // 'group' => $group, 
                
                'new_members' => $request->user_id,

                'message' => $request->user_ids ? 'Members added successfully' : 'Error adding members'
            
            ], 200);

        }
    }

    public function members($user_id, $groupId) {

        $group = Group::findOrFail($groupId);

        $participantsIds = $group->member_id;

        if(!empty($participantsIds)) {

            $participantsIds = explode(',', $participantsIds);

            if(!in_array($user_id, $participantsIds)) {

                $participantsIds[] = $user_id;

                $group->member_id = implode(',', $participantsIds);

            }

        } else {

            $group->member_id = $user_id;

        }

        $group->save();

    }

    public function addMessage(Request $request, $groupId)

    {

        $request->validate([

            'body' => 'required|string',

            'image_path' => 'nullable|string|max:255',

            'sender_id' => 'required',

            'group_id' => 'required'

        ]);

        $message = Message::create([

            'body' => $request->body,

            'image_path' => $request->image_path,

            'sender_id' => $request->sender_id,

            'group_id' => $groupId,

        ]);

        return response()->json([
            
            'message' => $message ? 'Message created successfully' : 'Error creating message',

            'data' => $message
        
        ], 200);
    }

    public function getGroup($user_id) {

        $group = Group::where('creator_id', $user_id)->get();

        return response([

            'message' => $group->isNotEmpty() ? 'Groups displayed successfully' : 'No groups found',

            'group' => $group

        ],200);

    }

}
