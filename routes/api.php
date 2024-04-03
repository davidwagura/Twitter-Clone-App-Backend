<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TweetController;


Route::post('/tweet', [TweetController::class, 'tweet']);
Route::post('/tweet/comment', [TweetController::class, 'comment']);