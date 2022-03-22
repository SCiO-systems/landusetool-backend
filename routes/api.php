<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\UserController;
use App\Http\Controllers\API\v1\InvitesController;
use App\Http\Controllers\API\v1\ProjectsController;
use App\Http\Controllers\API\v1\IndicatorsController;
use App\Http\Controllers\API\v1\UserAvatarController;
use App\Http\Controllers\API\v1\OAuth\ORCIDController;
use App\Http\Controllers\API\v1\ProjectFilesController;
use App\Http\Controllers\API\v1\UserPasswordController;
use App\Http\Controllers\API\v1\ProjectInvitesController;
use App\Http\Controllers\API\v1\ProjectScenariosController;
use App\Http\Controllers\API\v1\Integrations\ScioController;
use App\Http\Controllers\API\v1\ProjectFocusAreasController;
use App\Http\Controllers\API\v1\ProjectIndicatorsController;
use App\Http\Controllers\API\v1\ProjectLandUseMatrixController;
use App\Http\Controllers\API\v1\ProjectFocusAreaEvaluationsController;

// API v1
Route::prefix('v1')->name('api.v1.')->middleware('request.log')->group(function () {

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
    Route::middleware(['auth.jwt'])->group(function () {

        // PROJECTS

        // Project land use matrix.
        Route::get('projects/{project}/land_use_matrix', [
            ProjectLandUseMatrixController::class, 'index'
        ]);

        Route::put('projects/{project}/land_use_matrix', [
            ProjectLandUseMatrixController::class, 'update'
        ]);

        // Finalise project.
        Route::post('projects/{project}/finalise', [ProjectsController::class, 'finalise']);

        // Calculate polygon intersecting area in hectares.
        Route::post('projects/{project}/polygons/intersecting_area', [
            ScioController::class, 'getIntersectingArea'
        ])->name('projects.polygons.intersecting_area');

        // Land cover.
        Route::get('projects/{project}/land_cover_percentages', [
            ScioController::class, 'getLandCoverPercentages'
        ])->name('projects.land_cover_percentages');

        // Project proposed or final wocat technologies.
        Route::get('projects/{project}/wocat_technologies', [
            ProjectsController::class, 'getWocatTechnologies'
        ])->name('projects.wocat_technologies');

        // Propose a WOCAT technology.
        Route::post('projects/{project}/propose_wocat_technology', [
            ProjectsController::class, 'proposeWocatTechnology'
        ])->name('projects.propose_wocat_technology');

        // Vote for a WOCAT technology.
        Route::post('projects/{project}/vote_wocat_technology', [
            ProjectsController::class, 'voteWocatTechnology'
        ])->name('projects.vote_wocat_technology');

        // Project management.
        Route::apiResource('projects', ProjectsController::class);

        // Project indicators.
        Route::get('projects/{project}/indicators', [ProjectIndicatorsController::class, 'index']);
        Route::put('projects/{project}/indicators', [ProjectIndicatorsController::class, 'update']);

        // Project scenarios management.
        Route::delete('projects/{project}/scenarios', [
            ProjectScenariosController::class, 'clearScenarios'
        ]);

        Route::apiResource('projects.scenarios', ProjectScenariosController::class);

        Route::apiResource('projects.focus_areas', ProjectFocusAreasController::class)
            ->only(['index', 'store', 'destroy'])
            ->parameters(['focus_areas' => 'focusArea']);

        Route::apiResource('projects.focus_area_evaluations', ProjectFocusAreaEvaluationsController::class)
            ->only(['index', 'store', 'show', 'update'])
            ->parameters(['focus_area_evaluations' => 'focusAreaEvaluation']);

        // Project invites.
        Route::apiResource('projects.invites', ProjectInvitesController::class)
            ->only(['store', 'destroy']);

        // Invites.
        Route::apiResource('invites', InvitesController::class)
            ->only(['index', 'update']);

        // PROJECT FILES

        Route::apiResource('projects.files', ProjectFilesController::class);

        // INDICATORS

        // Indicators.
        Route::apiResource('indicators', IndicatorsController::class)->only(['index', 'store']);

        // POLYGONS

        // Admin level area polygons.
        Route::get('polygons/admin_level_areas', [
            ScioController::class, 'getAdminLevelAreaPolygons'
        ])->name('polygons.admin_level_areas');

        // Polygons by coordinates.
        Route::post('polygons/coordinates', [ScioController::class, 'getPolygonsByCoordinates'])
            ->name('polygons.coordinates');

        // LDN TARGETS

        // LDN targets.
        Route::get(
            '/country_level_links',
            [ScioController::class, 'getCountryLevelLinks']
        )->name('country_level_links');

        // WOCAT TECHNOLOGIES
        Route::get('/wocat_technologies', [ScioController::class, 'getWocatTechnologies'])
            ->name('wocat_technologies');
        Route::get('/wocat_technologies/{techId}', [ScioController::class, 'getWocatTechnology'])
            ->name('wocat_technologies.show');

        // USER

        // User management.
        Route::apiResource('users', UserController::class)->only(['index', 'show', 'update']);

        // User avatar management. Issue with file upload using PUT, must use POST.
        Route::post('/users/{user}/avatar', [UserAvatarController::class, 'update']);
        Route::delete('/users/{user}/avatar', [UserAvatarController::class, 'destroy']);

        // Update user password.
        Route::put('/users/{user}/password', [UserPasswordController::class, 'update']);
    });
});
