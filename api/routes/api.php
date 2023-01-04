<?php

use App\Http\Controllers\LevelController;
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
Route::get('users/{id}', [UserController::class, 'show']);
Route::patch('users/{id}', [UserController::class, 'update'])->middleware('verifyToken');

Route::post('token', [TokenController::class, 'generate']);
Route::get('token', [TokenController::class, 'decode'])->middleware('verifyToken');

Route::get('levels', [LevelController::class, 'index']);
Route::post('levels', [LevelController::class, 'store'])->middleware('verifyToken');
Route::get('levels/{id}', [LevelController::class, 'show']);
Route::patch('levels/{id}', [LevelController::class, 'update'])->middleware('verifyToken');
Route::delete('levels/{id}', [LevelController::class, 'destroy'])->middleware('verifyToken');
