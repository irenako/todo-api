<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskStatController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'index');
    Route::post('/users', 'store');
    Route::get('/users/{id}', 'show');
    Route::put('/users/{id}', 'update');
    Route::delete('/users/{id}', 'destroy');
});

Route::controller(TaskStatController::class)->group(function () {
    Route::get('/users/{userId}/tasks/stats', 'getUserTasksStats');
    Route::get('/tasks/stats', 'getAppTasksStats');
});

Route::controller(TaskController::class)->group(function () {
    Route::get('/users/{userId}/tasks', 'index');
    Route::post('/users/{userId}/tasks', 'store');
    Route::get('/users/{userId}/tasks/{taskId}', 'show');
    Route::put('/users/{userId}/tasks/{taskId}', 'update');
    Route::delete('/users/{userId}/tasks/{taskId}', 'destroy');
    Route::delete('/users/{userId}/tasks', 'deleteAllNewTasks');
});
