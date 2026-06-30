<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\ActivityLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Vertex-Admin
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api via bootstrap/app.php withRouting().
| Authentication uses Laravel Sanctum (cookie + bearer token).
| RBAC is handled by Spatie Permission v6 via the 'permission' middleware
| alias registered as CheckPermission in bootstrap/app.php.
|
*/

// -------------------------------------------------------------------------
// Public Auth Routes (rate-limited)
// -------------------------------------------------------------------------
Route::prefix('auth')->name('auth.')->group(function () {

    Route::middleware('throttle:5,1')->group(function () {
        Route::post('login', [AuthController::class, 'login'])
            ->name('login');

        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
            ->name('forgot-password');

        Route::post('reset-password', [AuthController::class, 'resetPassword'])
            ->name('reset-password');
    });

    // Protected auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])
            ->name('logout');

        Route::get('me', [AuthController::class, 'me'])
            ->name('me');
    });
});

// -------------------------------------------------------------------------
// Protected Routes (Sanctum guard)
// -------------------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // ---------------------------------------------------------------------
    // Profile
    // ---------------------------------------------------------------------
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])
            ->name('index');

        Route::put('/', [ProfileController::class, 'update'])
            ->name('update');

        Route::post('avatar', [ProfileController::class, 'uploadAvatar'])
            ->name('avatar');

        Route::post('change-password', [ProfileController::class, 'changePassword'])
            ->name('change-password');
    });

    // ---------------------------------------------------------------------
    // Dashboard
    // ---------------------------------------------------------------------
    Route::prefix('dashboard')->name('dashboard.')->middleware('permission:dashboard.view')->group(function () {
        Route::get('stats', [DashboardController::class, 'stats'])
            ->name('stats');

        Route::get('chart/registrations', [DashboardController::class, 'chartRegistrations'])
            ->name('chart.registrations');

        Route::get('chart/activity', [DashboardController::class, 'chartActivity'])
            ->name('chart.activity');

        Route::get('chart/roles-distribution', [DashboardController::class, 'chartRolesDistribution'])
            ->name('chart.roles-distribution');

        Route::get('recent-users', [DashboardController::class, 'recentUsers'])
            ->name('recent-users');

        Route::get('recent-activities', [DashboardController::class, 'recentActivities'])
            ->name('recent-activities');

        Route::get('latest-logins', [DashboardController::class, 'latestLogins'])
            ->name('latest-logins');
    });

    // ---------------------------------------------------------------------
    // Users
    // ---------------------------------------------------------------------
    Route::prefix('users')->name('users.')->group(function () {
        // Trashed list must be declared before the {id} wildcard route
        Route::get('trashed', [UserController::class, 'trashed'])
            ->middleware('permission:users.restore')
            ->name('trashed');

        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:users.view')
            ->name('index');

        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:users.create')
            ->name('store');

        Route::get('{id}', [UserController::class, 'show'])
            ->middleware('permission:users.view')
            ->name('show');

        Route::put('{id}', [UserController::class, 'update'])
            ->middleware('permission:users.edit')
            ->name('update');

        Route::delete('{id}', [UserController::class, 'destroy'])
            ->middleware('permission:users.delete')
            ->name('destroy');

        Route::post('{id}/restore', [UserController::class, 'restore'])
            ->middleware('permission:users.restore')
            ->name('restore');

        Route::post('{id}/activate', [UserController::class, 'activate'])
            ->middleware('permission:users.edit')
            ->name('activate');

        Route::post('{id}/deactivate', [UserController::class, 'deactivate'])
            ->middleware('permission:users.edit')
            ->name('deactivate');

        Route::post('{id}/assign-role', [UserController::class, 'assignRole'])
            ->middleware('permission:users.edit')
            ->name('assign-role');
    });

    // ---------------------------------------------------------------------
    // Roles
    // ---------------------------------------------------------------------
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->middleware('permission:roles.view')
            ->name('index');

        Route::post('/', [RoleController::class, 'store'])
            ->middleware('permission:roles.create')
            ->name('store');

        Route::get('{id}', [RoleController::class, 'show'])
            ->middleware('permission:roles.view')
            ->name('show');

        Route::put('{id}', [RoleController::class, 'update'])
            ->middleware('permission:roles.edit')
            ->name('update');

        Route::delete('{id}', [RoleController::class, 'destroy'])
            ->middleware('permission:roles.delete')
            ->name('destroy');

        Route::post('{id}/assign-permissions', [RoleController::class, 'assignPermissions'])
            ->middleware('permission:roles.edit')
            ->name('assign-permissions');

        Route::get('{id}/permissions', [RoleController::class, 'permissions'])
            ->middleware('permission:roles.view')
            ->name('permissions');
    });

    // ---------------------------------------------------------------------
    // Permissions
    // ---------------------------------------------------------------------
    Route::prefix('permissions')->name('permissions.')->group(function () {
        // Groups must be declared before the {id} wildcard route
        Route::get('groups', [PermissionController::class, 'groups'])
            ->middleware('permission:permissions.view')
            ->name('groups');

        Route::get('/', [PermissionController::class, 'index'])
            ->middleware('permission:permissions.view')
            ->name('index');

        Route::post('/', [PermissionController::class, 'store'])
            ->middleware('permission:permissions.create')
            ->name('store');

        Route::get('{id}', [PermissionController::class, 'show'])
            ->middleware('permission:permissions.view')
            ->name('show');

        Route::put('{id}', [PermissionController::class, 'update'])
            ->middleware('permission:permissions.edit')
            ->name('update');

        Route::delete('{id}', [PermissionController::class, 'destroy'])
            ->middleware('permission:permissions.delete')
            ->name('destroy');
    });

    // ---------------------------------------------------------------------
    // Settings
    // ---------------------------------------------------------------------
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])
            ->middleware('permission:settings.view')
            ->name('index');

        Route::put('general', [SettingController::class, 'updateGeneral'])
            ->middleware('permission:settings.edit')
            ->name('update-general');

        Route::put('email', [SettingController::class, 'updateEmail'])
            ->middleware('permission:settings.edit')
            ->name('update-email');

        Route::post('logo', [SettingController::class, 'uploadLogo'])
            ->middleware('permission:settings.edit')
            ->name('upload-logo');

        Route::post('favicon', [SettingController::class, 'uploadFavicon'])
            ->middleware('permission:settings.edit')
            ->name('upload-favicon');
    });

    // ---------------------------------------------------------------------
    // Audit Logs
    // ---------------------------------------------------------------------
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        // Clear must be declared before the {id} wildcard route
        Route::delete('clear', [AuditLogController::class, 'clear'])
            ->middleware('permission:audit.clear')
            ->name('clear');

        Route::get('/', [AuditLogController::class, 'index'])
            ->middleware('permission:audit.view')
            ->name('index');

        Route::get('{id}', [AuditLogController::class, 'show'])
            ->middleware('permission:audit.view')
            ->name('show');
    });

    // ---------------------------------------------------------------------
    // Activity Logs
    // ---------------------------------------------------------------------
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])
            ->middleware('permission:activity.view')
            ->name('index');

        Route::delete('clear', [ActivityLogController::class, 'clear'])
            ->middleware('permission:activity.clear')
            ->name('clear');
    });

    // ---------------------------------------------------------------------
    // Notifications
    // ---------------------------------------------------------------------
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])
            ->name('index');

        Route::get('unread-count', [NotificationController::class, 'unreadCount'])
            ->name('unread-count');

        Route::post('read-all', [NotificationController::class, 'readAll'])
            ->name('read-all');

        Route::post('{id}/read', [NotificationController::class, 'markAsRead'])
            ->name('read');

        Route::delete('{id}', [NotificationController::class, 'destroy'])
            ->name('destroy');
    });
});
