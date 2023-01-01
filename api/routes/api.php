<?php

use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::resource('users', UserController::class);

/**
 * UserController
 */
Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);
Route::get('users/{login}', [UserController::class, 'show']);
Route::patch('users/{login}', [UserController::class, 'update'])->middleware('verifyToken');

Route::post('token', [TokenController::class, 'generate']);
Route::get('token/whoami', [TokenController::class, 'getLogin'])->middleware('verifyToken'); // Vérifie la validité du token
