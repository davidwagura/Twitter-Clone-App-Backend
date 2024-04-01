<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/yoh', function () {
    return view('welcome');
});

Route::get('/', [DashboardController::class, 'index']);