<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TweetController;
use App\Models\Tweet;
use Tests\Feature\TweetForYouTest;

//home page

Route::get('/', [TweetController::class, 'home']);


//login to your account

Route::post('/login', [TweetController::class, 'login']);


//logout out of your account

Route::post('/logout', [TweetController::class, 'logout']);


//create a new tweet

Route::post('/tweet', [TweetController::class, 'tweet']);

//create a new user(register)

Route::post('/user', [TweetController::class, 'user']);


//comment on a tweet

Route::post('/tweet/comment', [TweetController::class, 'comment']);


//like a tweet

Route::post('/like/{tweet_id}/{user_id}', [TweetController::class, 'likeTweet']);

//like a comment

Route::post('/likeComment/{comment_id}/{user_id}', [TweetController::class, 'likeComment']);


//unlike a already liked tweet

Route::post('/unlike/{tweet_id}/{user_id}', [TweetController::class, 'unlikeTweet']);


//unlike comment

Route::post('/unlikeComment/{comment_id}/{user_id}', [TweetController::class, 'unlikeComment']);


//retweet a tweet

Route::post('/retweet/{tweet_id}/{user_id}', [TweetController::class, 'retweet']);


//remove a retweeted tweet

Route::post('/unretweet/{tweet_id}/{user_id}', [TweetController::class, 'unretweet']);


//retweet comment

Route::post('/retweetComment/{comment_id}/{user_id}', [TweetController::class, 'retweetComment']);


//unretweet comment

Route::post('/unretweetComment/{comment_id}/{user_id}', [TweetController::class, 'unretweetComment']);


//create comment comment

Route::post('/commentComment', [TweetController::class, 'addCommentComments']);


//reset user password

Route::post('/resetPassword/{user_id}', [TweetController::class, 'resetPassword']);


//route for users to follow you

Route::post('/followers/{follower_id}/{user_to_follow_id}', [TweetController::class, 'followers']);


//user following you unfollow route

Route::post('/unfollow/{follower_id}/{user_id}', [TweetController::class, 'followersUnFollow']);


//follow a user

Route::post('/following/{user_id}/{user_to_follow_id}', [TweetController::class, 'following']);


//unfollow a user you were following

Route::post('/unfollowing/{following_id}/{user_id}', [TweetController::class, 'followingUnFollow']);


//send a message to an user

Route::post('/messages/{sender_id}/{receivers_id}', [TweetController::class, 'messages']);


//create user profile

Route::post('/profile', [TweetController::class, 'createProfile']);


//create a group

Route::post('/group', [TweetController::class, 'usersGroup']);


//update user profile

Route::put('/update/{user_id}', [TweetController::class, 'editProfile']);

Route::post('/groups', [TweetController::class, 'createGroup']);

Route::post('/groups/{groupId}/messages', [TweetController::class, 'addMessage']);

Route::post('/groups/{groupId}/members', [TweetController::class, 'addMembers']);


//DELETE

//delete a user's tweet by it's Id

Route::delete('tweet/delete/{tweet_id}', [TweetController::class, 'deleteTweet']);


//delete a conversation between two users

Route::delete('/deleteConversation/{sender_id}/{receiver_id}', [TweetController::class, 'deleteConversation']);


//delete a single message in a conversation

Route::delete('/deleteOneMessage/{message_id}', [TweetController::class, 'deleteOneMessage']);





//[GET REQUESTS]



//get a tweet by it's Id

Route::get('/tweet/{id}', [TweetController::class, 'showTweet']);


//get  all the comments of a tweet by it's Id 

Route::get('/comments/{tweet_id}', [TweetController::class, 'comments']);


//get conversation

Route::get('/conversation/{sender_id}/', [TweetController::class, 'conversation']);


//get all users in DB 

Route::get('/users',[TweetController::class, 'getAllUsers']);


//get comment by Id

Route::get('/comment/{id}', [TweetController::class, 'getComment']);


//get conversation

Route::get('/messages/{sender_id}/{receivers_id}', [TweetController::class, 'getMessages']);


//get an user by id

Route::get('/user/{id}', [TweetController::class, 'getUser']);


//get all user tweets

Route::get('/user/tweets/{id}', [TweetController::class, 'userTweets']);


//get comment comments by comment_id

Route::get('/commentComment/{comment_id}', [TweetController::class, 'getCommentComments']);


//show the profile details of the user

Route::get('/getProfile/{user_id}', [TweetController::class, 'getProfile']);


//get all the users that are following me

Route::get('/myFollowers/{myId}', [TweetController::class, 'showFollowers']);


//show all the users I'm following

Route::get('/showFollowing/{myId}', [TweetController::class, 'showFollowing']);


//show the following and followers count

Route::get('/connectionCount/{myId}', [TweetController::class, 'connectionCount']);


//get all tweets liked by an user

Route::get('/userLikedTweets/{user_id}', [TweetController::class, 'getUserLikedTweets']);


//get all comments of a user

Route::get('/highlights/{user_id}', [TweetController::class, 'showHighlights']);

Route::get('/articles/{user_id}', [TweetController::class, 'showArticles']);

Route::get('/media/{user_id}', [TweetController::class, 'showMedia']);

//show all user comments

Route::get('/commented/comments/{user_id}', [TweetController::class, 'userComments']);

//get all messages

Route::get('/all/messages/{sender_id}', [TweetController::class, 'getAllMessages']);


//get all users I'm following tweets

Route::get('/followingTweets/{user_id}', [TweetController::class, 'followingTweets']);


//for you tweets api route

Route::get('/for-you', [TweetController::class, 'tweetsForYou']);

//trending tweets api

Route::get('/trends', [TweetController::class, 'trends']);

//user notifications api

Route::get('/notifications/{user_id}', [TweetController::class, 'getNotifications']);

//user mentions api

Route::get('/mentions/{created_by}/{user_id}', [TweetController::class, 'getMentions']);

//get conversations

Route::get('/conversations/{sender_id}/{receiver_id}', [TweetController::class, 'userConversations']);


//get groups 

Route::get('/user/groups/{user_id}', [TweetController::class, 'getGroup']);


//get group messages

Route::get('/group/messages/{group_id}', [TweetController::class, 'getGroupMessages']);
