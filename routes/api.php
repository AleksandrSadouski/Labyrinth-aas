<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\GameController;

Route::post('/auth/login', [AuthController::class, 'loginProfile']);
Route::post('/auth/register', [AuthController::class, 'registerProfile']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/menu/logout', [MenuController::class, 'exitProfile']);
    Route::delete('/menu/delete', [MenuController::class, 'deleteProfile']);
    Route::post('/menu/editor', [MenuController::class, 'createRoom']);
    Route::post('/menu/join', [MenuController::class, 'joinRoom']);
    Route::get('/menu/stats', [MenuController::class, 'showStatsProfile']);
    Route::patch('/menu/name', [MenuController::class, 'renameProfile']);
    Route::get('/menu/leaderboard/rating', [MenuController::class, 'showLeaderboardRating']);

    Route::post('/game/moves', [GameController::class, 'makeMove']);
    Route::post('/game/message', [GameController::class, 'writeMessage']);
    Route::post('/game/exit', [GameController::class, 'exitRoom']);
    Route::delete('/game/cancel', [GameController::class, 'cancelRoom']);
    Route::get('/poll', [GameController::class, 'checkRoom']);
});