<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TweetController;
use App\Models\Tweet;

Route::post('/tweet', [TweetController::class, 'tweet']);
Route::post('/tweet/comment', [TweetController::class, 'comment']);
Route::get('/tweet/{id}', [TweetController::class, 'showTweet']);
Route::get('/comment/{id}', [TweetController::class, 'tweetComments']);