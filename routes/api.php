<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\UserController;
use App\Http\Controllers\API\v1\InvitesController;
use App\Http\Controllers\API\v1\ProjectsController;
use App\Http\Controllers\API\v1\IndicatorsController;
use App\Http\Controllers\API\v1\UserAvatarController;
use App\Http\Controllers\API\v1\OAuth\ORCIDController;
use App\Http\Controllers\API\v1\UserPasswordController;
use App\Http\Controllers\API\v1\ProjectInvitesController;

// API v1
Route::prefix('v1')->name('api.v1.')->group(function () {

    // --- OAUTH ROUTES ---
    Route::prefix('oauth')->group(function () {

        // ORCID.
        Route::prefix('orcid')->group(function () {
            Route::get('/', [ORCIDController::class, 'redirect']);
            Route::get('/callback', [ORCIDController::class, 'callback']);
        });
    });

    // Authenticate a user.
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Logout a user.
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Register a new user.
    Route::post('/register', [UserController::class, 'store']);

    // Return the authenticated user or 401.
    Route::get('/auth/token/check', [AuthController::class, 'check']);

    // Authenticated and authorized (store) routes.
    Route::middleware(['auth.jwt', 'request.log'])->group(function () {

        // User management.
        Route::apiResource('users', UserController::class)->only(['index', 'show', 'update']);

        // Project management.
        Route::apiResource('projects', ProjectsController::class);

        // Project invites.
        Route::apiResource('projects.invites', ProjectInvitesController::class)
            ->only(['store', 'destroy']);

        // Invites.
        Route::apiResource('invites', InvitesController::class)
            ->only(['index', 'update']);

        // Indicators.
        Route::apiResource('indicators', IndicatorsController::class)->only(['index']);

        // User avatar management. Issue with file upload using PUT, must use POST.
        Route::post('/users/{user}/avatar', [UserAvatarController::class, 'update']);
        Route::delete('/users/{user}/avatar', [UserAvatarController::class, 'destroy']);

        // Update user password.
        Route::put('/users/{user}/password', [UserPasswordController::class, 'update']);
    });
});
