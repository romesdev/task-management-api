<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);

Route::middleware(['auth:sanctum'], \App\Http\Middleware\EnsureAuthenticated::class)->group(function () {

    Route::apiResource('tasks', TaskController::class);
});
