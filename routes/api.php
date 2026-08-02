<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\GameController;

Route::post('/auth/login', [AuthController::class, 'loginProfile']);
Route::post('/auth/register', [AuthController::class, 'registerProfile']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/menu/logout', [MenuController::class, 'exitProfile']);
    Route::post('/menu/editor', [MenuController::class, 'createRoom']);
    Route::post('/menu/join', [MenuController::class, 'joinRoom']);
    Route::get('/menu/stats', [MenuController::class, 'showStats']);

    Route::post('/game/moves', [GameController::class, 'makeMove']);
    Route::delete('/game/exit', [GameController::class, 'exitRoom']);
    Route::delete('/game/cancel', [GameController::class, 'cancelRoom']);
    Route::get('/poll', [GameController::class, 'checkRoom']);
});