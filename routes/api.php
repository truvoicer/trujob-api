<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Firebase\FirebaseDeviceController;
use App\Http\Controllers\Api\Firebase\FirebaseMessageController;
use App\Http\Controllers\Api\Firebase\FirebaseTopicController;
use App\Http\Controllers\Api\Listing\BrandController;
use App\Http\Controllers\Api\Listing\CategoryController;
use App\Http\Controllers\Api\Listing\ColorController;
use App\Http\Controllers\Api\Listing\ListingBrandController;
use App\Http\Controllers\Api\Listing\ListingCategoryController;
use App\Http\Controllers\Api\Listing\ListingColorController;
use App\Http\Controllers\Api\Listing\ListingController;
use App\Http\Controllers\Api\Listing\ListingMediaController;
use App\Http\Controllers\Api\Listing\ListingProductTypeController;
use App\Http\Controllers\Api\Listing\ProductTypeController;
use App\Http\Controllers\Api\Locale\CountryController;
use App\Http\Controllers\Api\Locale\CurrencyController;
use App\Http\Controllers\Api\Messaging\MessagingGroupController;
use App\Http\Controllers\Api\Messaging\MessagingGroupMessageController;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\Tools\FileSystemController;
use App\Http\Controllers\Api\User\RoleController;
use App\Http\Controllers\Api\User\UserSellerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\AppPublic;
use Illuminate\Support\Facades\Route;


Route::middleware(AppPublic::class)->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });

    Route::get('/page/{page:slug}', [PageController::class, 'edit'])->name('page.edit');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');

    Route::prefix('listing')->name('listing.')->group(function () {
        Route::get('/fetch', [ListingController::class, 'fetchPublicListings'])->name('fetch');
        Route::get('/category/fetch', [CategoryController::class, 'fetchCategories'])->name('category.fetch');
        Route::get('/brand/fetch', [BrandController::class, 'fetchBrands'])->name('brand.fetch');
        Route::get('/color/fetch', [ColorController::class, 'fetchColors'])->name('color.fetch');
        Route::get('/product-type/fetch', [ProductTypeController::class, 'fetchProductType'])->name('product_type.fetch');
        Route::prefix('{listing}')->name('item.')->group(function () {
            Route::get('/fetch', [ListingController::class, 'fetchSinglePublicListing'])->name('fetch');
        });
    });
});

Route::middleware(['auth:sanctum', 'ability:api:admin,api:superuser,api:super_admin,api:user,api:app_user'])->group(function () {
    Route::prefix('firebase')->name('firebase.')->group(function () {
        Route::prefix('device')->name('device.')->group(function () {
            Route::post('/register', [FirebaseDeviceController::class, 'registerFirebaseDevice'])->name('register');
        });
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::prefix('listing')->name('listing.')->group(function () {
            Route::get('/', [ListingController::class, 'fetchUserListings'])->name('index');
            Route::get('/{listing?}', [ListingController::class, 'fetchSinglePrivateListing'])->name('edit');
        });
    });
    Route::prefix('listing')->name('listing.')->group(function () {
        Route::post('/create', [ListingController::class, 'createListing'])->name('create');
        Route::get('/initialize', [ListingController::class, 'initializeListing'])->name('initialize');
        Route::prefix('category')->name('category.')->group(function () {
            Route::post('/create', [CategoryController::class, 'createCategory'])->name('create');
            Route::post('/{category}/update', [CategoryController::class, 'updateCategory'])->name('update');
            Route::post('/{category}/delete', [CategoryController::class, 'deleteCategory'])->name('delete');
        });
        Route::prefix('brand')->name('brand.')->group(function () {
            Route::post('/create', [BrandController::class, 'createBrand'])->name('create');
            Route::post('/{brand}/update', [BrandController::class, 'updateBrand'])->name('update');
            Route::delete('/{brand}/delete', [BrandController::class, 'deleteBrand'])->name('delete');
        });
        Route::prefix('color')->name('color.')->group(function () {
            Route::post('/create', [ColorController::class, 'createColor'])->name('create');
            Route::post('/{color}/update', [ColorController::class, 'updateColor'])->name('update');
            Route::delete('/{color}/delete', [ColorController::class, 'deleteColor'])->name('delete');
        });
        Route::prefix('product-type')->name('product_type.')->group(function () {
            Route::post('/create', [ProductTypeController::class, 'createProductType'])->name('create');
            Route::post('/{productType}/update', [CategoryController::class, 'updateCategory'])->name('update');
            Route::delete('/{productType}/delete', [CategoryController::class, 'deleteCategory'])->name('delete');
        });

        Route::prefix('{listing?}')->group(function () {
            Route::post('/save', [ListingController::class, 'saveListing'])->name('save');
            Route::post('/update', [ListingController::class, 'updateListing'])->name('update');
            Route::delete('/delete', [ListingController::class, 'deleteListing'])->name('delete');

            Route::prefix('category')->name('category.')->group(function () {
                Route::post('/{category}/add', [ListingCategoryController::class, 'addCategoryToListing'])->name('add');
                Route::post('/{category}/remove', [ListingCategoryController::class, 'removeCategoryFromListing'])->name('remove');
            });
            Route::prefix('brand')->name('brand.')->group(function () {
                Route::post('/{brand}/add', [ListingBrandController::class, 'addBrandToListing'])->name('add');
                Route::post('/{brand}/remove', [ListingBrandController::class, 'removeBrandFromListing'])->name('remove');
            });
            Route::prefix('color')->name('color.')->group(function () {
                Route::post('/{color}/add', [ListingColorController::class, 'addColorToListing'])->name('add');
                Route::post('/{color}/remove', [ListingColorController::class, 'removeColorFromListing'])->name('remove');
            });
            Route::prefix('product-type')->name('product_type.')->group(function () {
                Route::post('/{productType}/add', [ListingProductTypeController::class, 'addProductTypeToListing'])->name('add');
                Route::post('/{productType}/remove', [ListingProductTypeController::class, 'removeProductTypeFromListing'])->name('remove');
            });
            Route::prefix('messaging-group')->name('message_group.')->group(function () {
                Route::post('/create', [MessagingGroupController::class, 'createMessageGroup'])->name('create');
                Route::prefix('{messagingGroup}')->name('message_group.')->group(function () {
                    Route::post('/delete', [MessagingGroupController::class, 'deleteMessageGroup'])->name('delete');
                    Route::prefix('message')->name('message.')->group(function () {
                        Route::post('/create', [MessagingGroupMessageController::class, 'createMessage'])->name('create');
                        Route::prefix('/{messagingGroupMessage}')->group(function () {
                            Route::post('/update', [MessagingGroupMessageController::class, 'updateMessage'])->name('update');
                            Route::post('/delete', [MessagingGroupMessageController::class, 'deleteMessage'])->name('delete');
                        });
                    });
                });
            });
        });

        Route::prefix('media')->name('media.')->group(function () {

            Route::prefix('{listingMedia?}')->name('item.')->group(function () {
                Route::get('/fetch', [ListingMediaController::class, 'fetchMedia'])->name('fetch');
                Route::post('/update', [ListingMediaController::class, 'updateListingMedia'])->name('update');
                Route::delete('/delete', [ListingMediaController::class, 'deleteListingMedia'])->name('delete');
            });

            Route::get('/fetch', [ListingMediaController::class, 'fetchMedia'])->name('fetch');
            Route::post('/create', [ListingMediaController::class, 'createListingMedia'])->name('create');
        });
    });

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

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('/user')->name('user.')->group(function () {
            Route::post('/create', [AdminController::class, 'createUser'])->name('create');
            Route::prefix('{user}')->name('role.')->group(function () {
                Route::prefix('seller')->name('seller.')->group(function () {
                    Route::post('/add', [UserSellerController::class, 'addUserSeller'])->name('create');
                    Route::delete('/remove', [UserSellerController::class, 'removeUserSeller'])->name('delete');
                });
                Route::prefix('role')->name('role.')->group(function () {
                    Route::post('/{role}/update', [RoleController::class, 'updateUserRole'])->name('update');
                });
            });
        });
        Route::prefix('role')->name('role.')->group(function () {
            Route::post('/create', [RoleController::class, 'createRole'])->name('create');
            Route::post('/{role}/update', [RoleController::class, 'updateRole'])->name('update');
            Route::delete('/{role}/delete', [RoleController::class, 'deleteRole'])->name('delete');
        });
    });

    Route::prefix('firebase')->name('firebase.')->group(function () {
        Route::prefix('device')->name('device.')->group(function () {
            Route::prefix('messaging')->name('messaging.')->group(function () {
                Route::post('/send', [FirebaseMessageController::class, 'sendMessageToDevice'])->name('send');
            });
            Route::post('/register', [FirebaseDeviceController::class, 'registerFirebaseDevice'])->name('register');
            Route::post('/create', [FirebaseDeviceController::class, 'createFirebaseDevice'])->name('create');
            Route::prefix('{firebaseDevice}')->group(function () {
                Route::post('/update', [FirebaseDeviceController::class, 'updateFirebaseDevice'])->name('update');
                Route::post('/delete', [FirebaseDeviceController::class, 'deleteFirebaseDevice'])->name('delete');

            });
        });
        Route::prefix('topic')->name('topic.')->group(function () {
            Route::prefix('messaging')->name('messaging.')->group(function () {
                Route::post('/send', [FirebaseMessageController::class, 'sendMessageToTopic'])->name('send');
            });
            Route::prefix('{firebaseTopic}')->group(function () {
                Route::post('/update', [FirebaseTopicController::class, 'updateFirebaseTopic'])->name('update');
                Route::post('/delete', [FirebaseTopicController::class, 'deleteFirebaseTopic'])->name('delete');

            });
        });
    });

    Route::prefix('locale')->name('locale.')->group(function () {
        Route::prefix('country')->name('country.')->group(function () {
            Route::post('/create', [CountryController::class, 'createCountry'])->name('create');
            Route::post('/create/batch', [CountryController::class, 'createCountryBatch'])->name('create_batch');
            Route::post('/{country}/update', [CountryController::class, 'updateCountry'])->name('update');
            Route::post('/{country}/delete', [CountryController::class, 'deleteCountry'])->name('delete');
        });
        Route::prefix('currency')->name('currency.')->group(function () {
            Route::post('/create', [CurrencyController::class, 'createCurrency'])->name('create');
            Route::post('/create/batch', [CurrencyController::class, 'createCurrencyBatch'])->name('create_batch');
            Route::post('/{currency}/update', [CurrencyController::class, 'updateCurrency'])->name('update');
            Route::post('/{currency}/delete', [CurrencyController::class, 'deleteCurrency'])->name('delete');
        });
    });
});

