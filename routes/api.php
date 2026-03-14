<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\AnnouncementController;
use Illuminate\Support\Facades\Route;

// Public API
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Authenticated API
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::apiResource('members', MemberController::class)->only(['index', 'show', 'update']);
    Route::apiResource('events', EventController::class)->only(['index', 'show']);
    Route::post('/events/{event}/register', [EventController::class, 'register']);
    Route::delete('/events/{event}/unregister', [EventController::class, 'unregister']);
    Route::apiResource('announcements', AnnouncementController::class)->only(['index', 'show']);
});
