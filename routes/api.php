<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\GameController;

Route::post('/auth/login', [AuthController::class, 'loginProfile']);
Route::post('/auth/register', [AuthController::class, 'registerProfile']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/menu/logout', [MenuController::class, 'exitProfile'])->middleware('throttle:30,1');
    Route::delete('/menu/delete', [MenuController::class, 'deleteProfile'])->middleware('throttle:1,1');
    Route::post('/menu/editor', [MenuController::class, 'createRoom'])->middleware('throttle:10,1');
    Route::post('/menu/join', [MenuController::class, 'joinRoom'])->middleware('throttle:30,1');
    Route::get('/menu/stats', [MenuController::class, 'showStatsProfile'])->middleware('throttle:60,1');
    Route::get('/menu/stats/other', [MenuController::class, 'showStatsOtherProfile'])->middleware('throttle:60,1');
    Route::patch('/menu/name', [MenuController::class, 'renameProfile'])->middleware('throttle:5,1');
    Route::patch('/menu/password', [MenuController::class, 'changePassword'])->middleware('throttle:1,1');
    Route::put('/menu/reset', [MenuController::class, 'resetProfile'])->middleware('throttle:1,1');
    Route::get('/menu/leaderboard', [MenuController::class, 'showLeaderboard'])->middleware('throttle:60,1');

    Route::post('/game/moves', [GameController::class, 'makeMove'])->middleware('throttle:80,1');
    Route::post('/game/message', [GameController::class, 'writeMessage'])->middleware('throttle:30,1');
    Route::post('/game/exit', [GameController::class, 'exitRoom'])->middleware('throttle:30,1');
    Route::delete('/game/cancel', [GameController::class, 'cancelRoom'])->middleware('throttle:30,1');
    
    Route::get('/poll', [GameController::class, 'checkRoom'])->middleware('throttle:30,1');
});