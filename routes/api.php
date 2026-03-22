<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:12,1');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me', [AuthController::class, 'me'])->middleware(['auth:sanctum', 'active']);
});

Route::prefix('public')->group(function () {
    Route::get('settings', [PublicController::class, 'settings']);
    Route::get('projects', [PublicController::class, 'projects']);
    Route::get('projects/{slug}', [PublicController::class, 'projectBySlug']);
    Route::get('services', [PublicController::class, 'services']);
    Route::get('services/{slug}', [PublicController::class, 'serviceBySlug']);
    Route::get('pages/{slug}', [PublicController::class, 'pageBySlug']);
    Route::post('contact', [PublicController::class, 'contact'])->middleware('throttle:30,1');
    Route::post('support', [PublicController::class, 'support'])->middleware('throttle:30,1');
});

Route::middleware(['auth:sanctum', 'active'])->group(function () {
    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('projects/{project}', [ProjectController::class, 'show']);
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{service}', [ServiceController::class, 'show']);
    Route::get('pages', [PageController::class, 'index']);
    Route::get('pages/{page}', [PageController::class, 'show']);
    Route::get('messages', [MessageController::class, 'index']);
    Route::get('tickets', [TicketController::class, 'index']);
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::get('settings', [SettingsController::class, 'show']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/mark-read', [NotificationController::class, 'markRead'])->middleware('throttle:60,1');
});

Route::middleware(['auth:sanctum', 'active', 'not_viewer'])->group(function () {
    Route::post('projects', [ProjectController::class, 'store']);
    Route::put('projects/{project}', [ProjectController::class, 'update']);
    Route::delete('projects/{project}', [ProjectController::class, 'destroy']);

    Route::post('services', [ServiceController::class, 'store']);
    Route::put('services/{service}', [ServiceController::class, 'update']);
    Route::delete('services/{service}', [ServiceController::class, 'destroy']);

    Route::post('pages', [PageController::class, 'store']);
    Route::put('pages/{page}', [PageController::class, 'update']);
    Route::delete('pages/{page}', [PageController::class, 'destroy']);

    Route::patch('messages/{message}/read', [MessageController::class, 'markRead']);
    Route::delete('messages/{message}', [MessageController::class, 'destroy']);

    Route::put('tickets/{ticket}', [TicketController::class, 'update']);
    Route::patch('tickets/{ticket}/resolve', [TicketController::class, 'resolve']);
    Route::delete('tickets/{ticket}', [TicketController::class, 'destroy']);

    Route::post('users', [UserController::class, 'store']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);

    Route::put('settings', [SettingsController::class, 'update']);
});
