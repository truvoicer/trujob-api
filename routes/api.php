<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\Tools\FileSystemController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware(['auth:sanctum', 'ability:api:admin,api:superuser,api:super_admin,api:user,api:app_user'])->group(function () {
    Route::prefix('notification')->name('notification.')->group(function () {
        Route::get('/list', [NotificationController::class, 'index'])->name('list');
        Route::get('/read/count', [NotificationController::class, 'getReadCount'])->name('read.count');
        Route::get('/unread/count', [NotificationController::class, 'getUnreadCount'])->name('unread.count');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('/mark-all-unread', [NotificationController::class, 'markAllAsUnread'])->name('mark-all-unread');
        Route::get('/{notification}', [NotificationController::class, 'edit'])->name('detail');
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/{notification}/mark-unread', [NotificationController::class, 'markAsUnread'])->name('mark-unread');
        Route::delete('/{notification}/delete', [NotificationController::class, 'destroy'])->name('delete');
        Route::delete('/delete-all', [NotificationController::class, 'deleteAll'])->name('delete-all');
    });
});


Route::middleware(['auth:sanctum', 'ability:api:admin,api:superuser,api:super_admin,api:user,api:app_user'])->group(function () {

        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/api-token/generate', [AuthController::class, 'newToken'])->name('token.generate');
            Route::prefix('account')->name('account.')->group(function () {
                Route::post('/details', [AuthController::class, 'getAccountDetails'])->name('details');
            });
            Route::prefix('token')->name('token.')->group(function () {
                Route::get('/validate', [AuthController::class, 'validateToken'])->name('validate');
                Route::get('/user', [AuthController::class, 'getSingleUserByApiToken'])->name('user');
            });
        });

        Route::prefix('session')->name('session.')->group(function () {
            Route::prefix('user')->name('user.')->group(function () {
                Route::get('/detail', [UserController::class, 'getSessionUserDetail'])->name('detail');
                Route::patch('/update', [UserController::class, 'updateSessionUser'])->name('update');
                Route::get('/api-token', [UserController::class, 'getSessionUserApiToken'])->name('api-token.detail');
                Route::prefix('api-token')->name('api-token.')->group(function () {
                    Route::get('/list', [UserController::class, 'getSessionUserApiTokenList'])->name('list');
                    Route::get('/generate', [UserController::class, 'generateSessionUserApiToken'])->name('generate');
                    Route::delete('/delete', [UserController::class, 'deleteSessionUserApiToken'])->name('delete');
                });
            });
        });
});


Route::middleware(['auth:sanctum', 'ability:api:admin,api:superuser,api:super_admin,api:user'])->group(function () {
        Route::prefix('tools')->name('tools.')->group(function () {
            Route::prefix('filesystem')->name('filesystem.')->group(function () {
                Route::get('/list', [FileSystemController::class, 'getFiles'])->name('list');
                Route::get('/{file}', [FileSystemController::class, 'getSingleFile'])->name('detail');
                Route::prefix('{file}')->name('single.')->group(function () {
                    Route::get('/download', [FileSystemController::class, 'downloadFile'])->name('download');
                    Route::post('/delete', [FileSystemController::class, 'deleteFile'])->name('delete');
                });
            });
        });
});

Route::middleware(['auth:sanctum', 'ability:api:superuser,'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::prefix('user')->name('user.')->group(function () {
                Route::get('/role/list', [AdminController::class, 'getUserRoleList'])->name('role.list');
            });
        });

        Route::prefix('permission')->name('permission.')->group(function () {
            Route::get('/{permission}', [PermissionController::class, 'getSinglePermission'])->name('detail');
            Route::post('/{permission}/update', [PermissionController::class, 'updatePermission'])->name('update');
            Route::get('/list', [PermissionController::class, 'getPermissions'])->name('list');
            Route::post('/create', [PermissionController::class, 'createPermission'])->name('create');
            Route::post('/delete', [PermissionController::class, 'deletePermission'])->name('delete');
        });
});

Route::middleware(['auth:sanctum', 'ability:api:admin,api:superuser,api:super_admin'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::prefix('user')->name('user.')->group(function () {
                Route::get('/list', [AdminController::class, 'getUsersList'])->name('list');
                Route::post('/create', [AdminController::class, 'createUser'])->name('create');
                Route::prefix('batch')->name('batch.')->group(function () {
                    Route::delete('/delete', [AdminController::class, 'deleteBatchUser'])->name('delete');
                });
                Route::get('/{user}', [AdminController::class, 'getSingleUser'])->name('detail');
                Route::prefix('{user}')->name('single.')->group(function () {
                    Route::patch('/update', [AdminController::class, 'updateUser'])->name('update');
                    Route::delete('/delete', [AdminController::class, 'deleteUser'])->name('delete');
                    Route::prefix('api-token')->name('api-token.')->group(function () {
                        Route::get('/list', [AdminController::class, 'getUserApiTokens'])->name('list');
                        Route::post('/generate', [AdminController::class, 'generateNewApiToken'])->name('generate');
                        Route::post('/delete', [AdminController::class, 'deleteSessionUserApiToken'])->name('session.delete');
                        Route::get('/{personalAccessToken}', [AdminController::class, 'getApiToken'])->name('detail');
                        Route::patch('/{personalAccessToken}/update', [AdminController::class, 'updateApiTokenExpiry'])->name('update');
                        Route::delete('/{personalAccessToken}/delete', [AdminController::class, 'deleteApiToken'])->name('delete');
                    });
                });
            });
        });
});
