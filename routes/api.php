<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TweetController;


Route::post('/login', [TweetController::class, 'login']);

Route::post('/logout', [TweetController::class, 'logout']);

Route::get('/', [TweetController::class, 'home']);

Route::post('/tweet', [TweetController::class, 'tweet']);

Route::post('/user', [TweetController::class, 'user']);

Route::post('/tweet/comment', [TweetController::class, 'comment']);

Route::post('/like/{tweet_id}/{user_id}', [TweetController::class, 'likeTweet']);

Route::post('/unlike/{tweet_id}/{likes_id}', [TweetController::class, 'unlikeTweet']);

Route::post('/retweet/{tweet_id}/{user_id}', [TweetController::class, 'retweet']);

Route::post('/unretweet/{tweet_id}/{likes_id}', [TweetController::class, 'unretweet']);

Route::post('/resetPassword/{user_id}', [TweetController::class, 'resetPassword']);

Route::post('/followers/{follower_id}/{user_to_follow_id}', [TweetController::class, 'followers']);

Route::post('/unfollow/{follower_id}/{user_id}', [TweetController::class, 'followersUnFollow']);

Route::post('/following/{following_id}/{user_to_follow_id}', [TweetController::class, 'following']);

Route::post('/unfollowing/{following_id}/{user_id}', [TweetController::class, 'followingUnFollow']);

Route::post('/messages/{sender_id}/{receiver_id}', [TweetController::class, 'messages']);

Route::post('/deleteOneMessage/{message_id}', [TweetController::class, 'deleteOneMessage']);

Route::delete('/deleteConversation/{sender_id}/{receiver_id}', [TweetController::class, 'deleteConversation']);




//get a tweet by it's Id

Route::get('/tweet/{id}', [TweetController::class, 'showTweet']);


//get  all the comments of a tweet by it's Id

Route::get('/comments/{id}', [TweetController::class, 'comments']);


//get all user tweets

Route::get('/user/tweets/{id}', [TweetController::class, 'userTweets']);


//get all user comments

Route::get('tweet/comments/{id}', [TweetController::class, 'tweetComments']);


//show the profile details of the user

Route::get('/profile/{user_id}', [TweetController::class, 'profile']);


//delete a user's tweet by it's Id

Route::delete('tweet/delete/{tweet_id}', [TweetController::class, 'deleteTweet']);


//get all the users that are following me

Route::get('/myFollowers/{myId}', [TweetController::class, 'showFollowers']);


//show all the users I'm following

Route::get('/showFollowing/{myId}',[TweetController::class, 'showFollowing']);


//show the following and followers count

Route::get('/connectionCount/{myId}', [TweetController::class, 'connectionCount']);


//get all tweets liked by an user

Route::get('/userLikedTweets/{user_id}', [TweetController::class, 'getUserLikedTweets']);


//get all comments of a user

Route::get('/userTweetComments/{user_id}', [TweetController::class, 'getUserTweetComments']);

Route::get('/highlights/{user_id}', [TweetController::class, 'showHighlights']);

Route::get('/articles/{user_id}', [TweetController::class, 'showArticles']);

Route::get('/media/{user_id}', [TweetController::class, 'showMedia']);


//get all users I'm following tweets

Route::get('/followingTweets/{user_id}',[TweetController::class, 'followingTweets']);


//for you tweets api route

Route::get('/for you',[TweetController::class,'tweetForYou']);