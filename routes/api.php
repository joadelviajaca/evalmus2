<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\EvaluationController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);

    Route::post('/evaluations', [EvaluationController::class, 'store']);
    Route::post('/evaluations/{evaluation}/submit', [EvaluationController::class, 'submit']);
    Route::get('/evaluations/project/{project}', [EvaluationController::class, 'show']);
});
