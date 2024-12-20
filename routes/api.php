<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::apiResource('tasks', TaskController::class);
});
