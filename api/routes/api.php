<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


/**
 * UserController
 */
Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);
Route::get('users/{id}', [UserController::class, 'show']);
Route::patch('users/{id}', [UserController::class, 'update'])->middleware('verifyToken');


/*
 * TokenController
 */
Route::post('token', [TokenController::class, 'generate']);
Route::get('token', [TokenController::class, 'decode'])->middleware('verifyToken');


/*
 * LevelController
 */
Route::get('levels', [LevelController::class, 'index']);
Route::post('levels', [LevelController::class, 'store'])->middleware('verifyToken');
Route::get('levels/{id}', [LevelController::class, 'show']);
Route::patch('levels/{id}', [LevelController::class, 'update'])->middleware('verifyToken');
Route::delete('levels/{id}', [LevelController::class, 'destroy'])->middleware('verifyToken');


/*
 * GameController
 */
Route::get('games', [GameController::class, 'index']);
Route::post('games', [GameController::class, 'store'])->middleware('verifyToken');
Route::get('games/{id}', [GameController::class, 'show']);
Route::get('games/byUserId/{id}', [GameController::class, 'showByUser']);
Route::patch('games/{id}', [GameController::class, 'update'])->middleware('verifyToken');
