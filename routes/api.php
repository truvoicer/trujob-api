<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\Auth\ApiTokenController;
use App\Http\Controllers\Api\Auth\Session\SessionApiTokenController;
use App\Http\Controllers\Api\Auth\AuthLoginController;
use App\Http\Controllers\Api\Auth\AuthRegisterController;
use App\Http\Controllers\Api\Auth\AuthUserController;
use App\Http\Controllers\Api\Auth\Session\SessionUserController;
use App\Http\Controllers\Api\Block\BlockController;
use App\Http\Controllers\Api\Block\BlockSidebarController;
use App\Http\Controllers\Api\User\BulkUserDeleteController;
use App\Http\Controllers\Api\Firebase\FirebaseDeviceController;
use App\Http\Controllers\Api\Firebase\FirebaseMessageController;
use App\Http\Controllers\Api\Firebase\FirebaseTopicController;
use App\Http\Controllers\Api\Listing\BrandController;
use App\Http\Controllers\Api\Listing\CategoryController;
use App\Http\Controllers\Api\Listing\ColorController;
use App\Http\Controllers\Api\Listing\InitialiseListingController;
use App\Http\Controllers\Api\Listing\ListingBrandController;
use App\Http\Controllers\Api\Listing\ListingCategoryController;
use App\Http\Controllers\Api\Listing\ListingColorController;
use App\Http\Controllers\Api\Listing\ListingController;
use App\Http\Controllers\Api\Listing\ListingMediaController;
use App\Http\Controllers\Api\Listing\ListingProductTypeController;
use App\Http\Controllers\Api\Listing\ListingPublicController;
use App\Http\Controllers\Api\Listing\ProductTypeController;
use App\Http\Controllers\Api\Listing\UserListingController;
use App\Http\Controllers\Api\Locale\CountryController;
use App\Http\Controllers\Api\Locale\CurrencyController;
use App\Http\Controllers\Api\Menu\AppMenuController;
use App\Http\Controllers\Api\Menu\AppMenuItemController;
use App\Http\Controllers\Api\Menu\MenuController;
use App\Http\Controllers\Api\Menu\MenuItemController;
use App\Http\Controllers\Api\Menu\MenuItemTypeController;
use App\Http\Controllers\Api\Sidebar\SidebarWidgetController;
use App\Http\Controllers\Api\Messaging\MessagingGroupController;
use App\Http\Controllers\Api\Messaging\MessagingGroupMessageController;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\Page\BatchDeletePageBlockController;
use App\Http\Controllers\Api\Page\PageBlockController;
use App\Http\Controllers\Api\Page\PageController;
use App\Http\Controllers\Api\Page\PageViewController;
use App\Http\Controllers\Api\Page\SitePageController;
use App\Http\Controllers\Api\Link\LinkTargetController;
use App\Http\Controllers\Api\Listing\FeatureController;
use App\Http\Controllers\Api\Listing\ListingFeatureController;
use App\Http\Controllers\Api\Listing\ListingTypeController;
use App\Http\Controllers\Api\Menu\MenuBulkDeleteController;
use App\Http\Controllers\Api\Menu\MenuItemMenuController;
use App\Http\Controllers\Api\Menu\MenuItemMenuReorderController;
use App\Http\Controllers\Api\Menu\MenuItemReorderController;
use App\Http\Controllers\Api\Menu\MenuItemRoleController;
use App\Http\Controllers\Api\Menu\MenuRoleController;
use App\Http\Controllers\Api\Page\Block\PageBlockReorderController;
use App\Http\Controllers\Api\Page\Block\PageBlockRoleController;
use App\Http\Controllers\Api\Page\Block\Sidebar\PageBlockSidebarController;
use App\Http\Controllers\Api\Page\Block\Sidebar\PageBlockSidebarReorderController;
use App\Http\Controllers\Api\Page\PageBulkDeleteController;
use App\Http\Controllers\Api\Page\PageRoleController;
use App\Http\Controllers\Api\Page\Sidebar\PageSidebarController;
use App\Http\Controllers\Api\Page\Sidebar\PageSidebarReorderController;
use App\Http\Controllers\Api\Sidebar\SidebarRoleController;
use App\Http\Controllers\Api\Pagination\PaginationTypeController;
use App\Http\Controllers\Api\Pagination\PaginationScrollTypeController;
use App\Http\Controllers\Api\Site\SiteController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\Sidebar\SidebarBulkDeleteController;
use App\Http\Controllers\Api\Sidebar\SidebarController;
use App\Http\Controllers\Api\Sidebar\SidebarWidgetReorderController;
use App\Http\Controllers\Api\Sidebar\SidebarWidgetRoleController;
use App\Http\Controllers\Api\Site\SiteTokenController;
use App\Http\Controllers\Api\Tools\FileSystemController;
use App\Http\Controllers\Api\User\RoleController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\User\UserSellerController;
use App\Http\Controllers\Api\Widget\WidgetBulkDeleteController;
use App\Http\Controllers\Api\Widget\WidgetController;
use App\Http\Controllers\Api\Widget\WidgetRoleController;
use App\Http\Middleware\AppPublic;
use Illuminate\Support\Facades\Route;


Route::middleware(AppPublic::class)->group(function () {});

Route::middleware(['auth:sanctum', 'ability:api:admin,api:superuser,api:super_admin,api:site'])->group(function () {

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');

    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('/view', [AuthUserController::class, 'view'])->name('view');
        Route::post('/login', AuthLoginController::class)->name('login');
        Route::post('/register', AuthRegisterController::class)->name('register');
    });
    Route::prefix('site')->name('site.')->group(function () {
        Route::get('/page', [SitePageController::class, 'view'])->name('page.view');
        Route::prefix('{site:name}')->group(function () {
            Route::get('/', [SiteController::class, 'view'])->name('view');
        });
    });


    Route::prefix('listing')->name('listing.')->group(function () {
        Route::get('/', [ListingPublicController::class, 'index'])->name('index');
        Route::prefix('{listing}')->name('item.')->group(function () {
            Route::get('/fetch', [ListingController::class, 'view'])->name('fetch');
        });
    });

    Route::prefix('app-menu')->name('app_menu.')->group(function () {
        Route::prefix('{appMenu}')->group(function () {
            Route::get('/', [AppMenuController::class, 'view'])->name('view');
        });
    });
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::prefix('{menu}')->group(function () {
            Route::get('/', [MenuController::class, 'view'])->name('view');
        });
    });
});

Route::middleware(['auth:sanctum', 'ability:api:admin,api:superuser,api:super_admin,api:site,api:user,api:app_user'])->group(function () {
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/brand', [BrandController::class, 'index'])->name('brand.index');
    Route::get('/color', [ColorController::class, 'index'])->name('color.index');
    Route::get('/product-type', [ProductTypeController::class, 'index'])->name('product_type.index');
    Route::get('/listing-type', [ListingTypeController::class, 'index'])->name('listing-type.index');
    Route::get('/feature', [FeatureController::class, 'index'])->name('feature.index');
});
Route::middleware(['auth:sanctum', 'ability:api:admin,api:superuser,api:super_admin,api:user,api:app_user'])->group(function () {
    Route::prefix('firebase')->name('firebase.')->group(function () {
        Route::prefix('device')->name('device.')->group(function () {
            Route::post('/register', [FirebaseDeviceController::class, 'registerFirebaseDevice'])->name('register');
        });
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::prefix('listing')->name('listing.')->group(function () {
            Route::get('/', [UserListingController::class, 'index'])->name('index');
            Route::get('/{listing?}', [UserListingController::class, 'view'])->name('edit');
        });
    });
    Route::prefix('listing')->name('listing.')->group(function () {
        Route::post('/create', [ListingController::class, 'create'])->name('create');
        Route::get('/initialize', InitialiseListingController::class)->name('initialize');
        Route::prefix('category')->name('category.')->group(function () {
            Route::post('/create', [CategoryController::class, 'createCategory'])->name('create');
            Route::patch('/{category}/update', [CategoryController::class, 'updateCategory'])->name('update');
            Route::delete('/{category}/delete', [CategoryController::class, 'deleteCategory'])->name('delete');
        });
        Route::prefix('brand')->name('brand.')->group(function () {
            Route::post('/create', [BrandController::class, 'createBrand'])->name('create');
            Route::patch('/{brand}/update', [BrandController::class, 'updateBrand'])->name('update');
            Route::delete('/{brand}/delete', [BrandController::class, 'deleteBrand'])->name('delete');
        });
        Route::prefix('color')->name('color.')->group(function () {
            Route::post('/create', [ColorController::class, 'createColor'])->name('create');
            Route::patch('/{color}/update', [ColorController::class, 'updateColor'])->name('update');
            Route::delete('/{color}/delete', [ColorController::class, 'deleteColor'])->name('delete');
        });
        Route::prefix('product-type')->name('product_type.')->group(function () {
            Route::post('/create', [ProductTypeController::class, 'createProductType'])->name('create');
            Route::patch('/{productType}/update', [CategoryController::class, 'updateCategory'])->name('update');
            Route::delete('/{productType}/delete', [CategoryController::class, 'deleteCategory'])->name('delete');
        });

        Route::prefix('{listing?}')->group(function () {
            Route::patch('/update', [ListingController::class, 'update'])->name('update');
            Route::delete('/delete', [ListingController::class, 'destroy'])->name('delete');

            Route::prefix('follow')->name('follow.')->group(function () {
                Route::get('/', [ListingCategoryController::class, 'index'])->name('index');
                Route::post('/{follow}/add', [ListingCategoryController::class, 'addCategoryToListing'])->name('add');
                Route::delete('/{follow}/remove', [ListingCategoryController::class, 'removeCategoryFromListing'])->name('remove');
            });
            Route::prefix('category')->name('category.')->group(function () {
                Route::get('/', [ListingCategoryController::class, 'index'])->name('index');
                Route::post('/{category}/add', [ListingCategoryController::class, 'addCategoryToListing'])->name('add');
                Route::delete('/{category}/remove', [ListingCategoryController::class, 'removeCategoryFromListing'])->name('remove');
            });
            Route::prefix('brand')->name('brand.')->group(function () {
                Route::get('/', [ListingBrandController::class, 'index'])->name('index');
                Route::post('/{brand}/add', [ListingBrandController::class, 'addBrandToListing'])->name('add');
                Route::delete('/{brand}/remove', [ListingBrandController::class, 'removeBrandFromListing'])->name('remove');
            });
            Route::prefix('color')->name('color.')->group(function () {
                Route::get('/', [ListingColorController::class, 'index'])->name('index');
                Route::post('/{color}/add', [ListingColorController::class, 'addColorToListing'])->name('add');
                Route::delete('/{color}/remove', [ListingColorController::class, 'removeColorFromListing'])->name('remove');
            });
            Route::prefix('product-type')->name('product_type.')->group(function () {
                Route::get('/', [ListingProductTypeController::class, 'index'])->name('index');
                Route::post('/{productType}/add', [ListingProductTypeController::class, 'addProductTypeToListing'])->name('add');
                Route::delete('/{productType}/remove', [ListingProductTypeController::class, 'removeProductTypeFromListing'])->name('remove');
            });
            Route::prefix('messaging-group')->name('message_group.')->group(function () {
                Route::post('/create', [MessagingGroupController::class, 'createMessageGroup'])->name('create');
                Route::prefix('{messagingGroup}')->name('message_group.')->group(function () {
                    Route::delete('/delete', [MessagingGroupController::class, 'deleteMessageGroup'])->name('delete');
                    Route::prefix('message')->name('message.')->group(function () {
                        Route::post('/create', [MessagingGroupMessageController::class, 'createMessage'])->name('create');
                        Route::prefix('/{messagingGroupMessage}')->group(function () {
                            Route::patch('/update', [MessagingGroupMessageController::class, 'updateMessage'])->name('update');
                            Route::delete('/delete', [MessagingGroupMessageController::class, 'deleteMessage'])->name('delete');
                        });
                    });
                });
            });
        });

        Route::prefix('media')->name('media.')->group(function () {

            Route::prefix('{listingMedia?}')->name('item.')->group(function () {
                Route::get('/fetch', [ListingMediaController::class, 'fetchMedia'])->name('fetch');
                Route::patch('/update', [ListingMediaController::class, 'updateListingMedia'])->name('update');
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

    Route::prefix('session')->name('session.')->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/view', [SessionUserController::class, 'view'])->name('view');
            Route::patch('/update', [SessionUserController::class, 'update'])->name('update');
            Route::prefix('api-token')->name('api-token.')->group(function () {
                Route::get('/', [SessionApiTokenController::class, 'index'])->name('index');
                Route::get('/view', [SessionApiTokenController::class, 'view'])->name('view');
                Route::get('/create', [SessionApiTokenController::class, 'create'])->name('create');
                Route::delete('/delete', [SessionApiTokenController::class, 'destroy'])->name('delete');
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
                Route::delete('/delete', [FileSystemController::class, 'deleteFile'])->name('delete');
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
        Route::patch('/{permission}/update', [PermissionController::class, 'updatePermission'])->name('update');
        Route::get('/list', [PermissionController::class, 'getPermissions'])->name('list');
        Route::post('/create', [PermissionController::class, 'createPermission'])->name('create');
        Route::delete('/delete', [PermissionController::class, 'deletePermission'])->name('delete');
    });
});

Route::middleware(['auth:sanctum', 'ability:api:admin,api:superuser,api:super_admin'])->group(function () {

    Route::prefix('listing')->name('listing.')->group(function () {
        Route::get('/', [ListingController::class, 'index'])->name('index');
    });

    Route::prefix('/user')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::delete('/delete', BulkUserDeleteController::class)->name('delete');
        });
        Route::patch('/create', [UserController::class, 'create'])->name('create');
        Route::prefix('{user}')->group(function () {
            Route::get('/', [UserController::class, 'view'])->name('detail');
            Route::patch('/update', [UserController::class, 'update'])->name('update');
            Route::delete('/delete', [UserController::class, 'destroy'])->name('delete');
            Route::prefix('api-token')->name('api-token.')->group(function () {
                Route::get('/', [ApiTokenController::class, 'index'])->name('index');
                Route::post('/create', [ApiTokenController::class, 'create'])->name('create');
                Route::prefix('{personalAccessToken}')->group(function () {
                    Route::get('/', [ApiTokenController::class, 'view'])->name('view');
                    Route::patch('/update', [ApiTokenController::class, 'update'])->name('update');
                    Route::delete('/delete', [ApiTokenController::class, 'destroy'])->name('delete');
                });
            });
            Route::prefix('seller')->name('seller.')->group(function () {
                Route::post('/add', [UserSellerController::class, 'addUserSeller'])->name('create');
                Route::delete('/remove', [UserSellerController::class, 'removeUserSeller'])->name('delete');
            });
            Route::prefix('role')->name('role.')->group(function () {
                Route::patch('/{role}/update', [RoleController::class, 'update'])->name('update');
            });
        });
    });

    Route::prefix('role')->name('role.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('detail');
        Route::post('/create', [RoleController::class, 'create'])->name('create');
        Route::patch('/{role}/update', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}/delete', [RoleController::class, 'delete'])->name('delete');
    });

    Route::prefix('firebase')->name('firebase.')->group(function () {
        Route::prefix('device')->name('device.')->group(function () {
            Route::prefix('messaging')->name('messaging.')->group(function () {
                Route::post('/send', [FirebaseMessageController::class, 'sendMessageToDevice'])->name('send');
            });
            Route::post('/register', [FirebaseDeviceController::class, 'registerFirebaseDevice'])->name('register');
            Route::post('/create', [FirebaseDeviceController::class, 'createFirebaseDevice'])->name('create');
            Route::prefix('{firebaseDevice}')->group(function () {
                Route::patch('/update', [FirebaseDeviceController::class, 'updateFirebaseDevice'])->name('update');
                Route::delete('/delete', [FirebaseDeviceController::class, 'deleteFirebaseDevice'])->name('delete');
            });
        });
        Route::prefix('topic')->name('topic.')->group(function () {
            Route::prefix('messaging')->name('messaging.')->group(function () {
                Route::post('/send', [FirebaseMessageController::class, 'sendMessageToTopic'])->name('send');
            });
            Route::prefix('{firebaseTopic}')->group(function () {
                Route::patch('/update', [FirebaseTopicController::class, 'updateFirebaseTopic'])->name('update');
                Route::delete('/delete', [FirebaseTopicController::class, 'deleteFirebaseTopic'])->name('delete');
            });
        });
    });

    Route::prefix('locale')->name('locale.')->group(function () {
        Route::prefix('country')->name('country.')->group(function () {
            Route::post('/create', [CountryController::class, 'createCountry'])->name('create');
            Route::post('/create/batch', [CountryController::class, 'createCountryBatch'])->name('create_batch');
            Route::patch('/{country}/update', [CountryController::class, 'updateCountry'])->name('update');
            Route::delete('/{country}/delete', [CountryController::class, 'deleteCountry'])->name('delete');
        });
        Route::prefix('currency')->name('currency.')->group(function () {
            Route::post('/create', [CurrencyController::class, 'createCurrency'])->name('create');
            Route::post('/create/batch', [CurrencyController::class, 'createCurrencyBatch'])->name('create_batch');
            Route::patch('/{currency}/update', [CurrencyController::class, 'updateCurrency'])->name('update');
            Route::delete('/{currency}/delete', [CurrencyController::class, 'deleteCurrency'])->name('delete');
        });
    });
    Route::prefix('block')->name('block.')->group(function () {
        Route::get('/', [BlockController::class, 'index'])->name('index');
        // Route::post('/create', [PageBlockController::class, 'create'])->name('create');
        Route::prefix('{block}')->group(function () {
            Route::get('/', [BlockController::class, 'view'])->name('view');
            Route::get('/sidebar', [BlockSidebarController::class, 'index'])->name('sidebar');

            // Route::patch('/update', [PageBlockController::class, 'update'])->name('update');
            // Route::delete('/delete', [PageBlockController::class, 'destroy'])->name('delete');
        });
    });

    Route::prefix('pagination')->name('pagination.')->group(function () {
        Route::get('/type', PaginationTypeController::class)->name('type');
        Route::get('/scroll/type', PaginationScrollTypeController::class)->name('scroll.type');
    });
    Route::prefix('page')->name('page.')->group(function () {
        Route::get('/', [PageController::class, 'index'])->name('index');
        Route::get('/view', [PageViewController::class, 'index'])->name('view.index');
        Route::post('/create', [PageController::class, 'create'])->name('create');
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::delete('/delete', PageBulkDeleteController::class)->name('delete');
        });
        Route::prefix('{page}')->group(function () {
            Route::get('/', [PageController::class, 'view'])->name('view');
            Route::patch('/update', [PageController::class, 'update'])->name('update');
            Route::delete('/delete', [PageController::class, 'delete'])->name('delete');
            Route::prefix('role')->name('role.')->group(function () {
                Route::get('/', [PageRoleController::class, 'index'])->name('index');
                Route::prefix('{role}')->group(function () {
                    Route::post('/create', [PageRoleController::class, 'create'])->name('create');
                    Route::delete('/delete', [PageRoleController::class, 'destroy'])->name('delete');
                });
            });
            Route::prefix('sidebar')->name('sidebar.')->group(function () {
                Route::get('/', [PageSidebarController::class, 'index'])->name('index');
                Route::prefix('{sidebar}')->group(function () {
                    Route::post('/create', [PageSidebarController::class, 'create'])->name('create');
                    Route::delete('/delete', [PageSidebarController::class, 'destroy'])->name('destroy');
                });
                Route::prefix('reorder')->name('reorder.')->group(function () {
                    Route::post('/', PageSidebarReorderController::class)->name('reorder');
                });
            });
            Route::prefix('block')->name('block.')->group(function () {
                Route::get('/', [PageBlockController::class, 'index'])->name('index');
                Route::post('/batch/delete', BatchDeletePageBlockController::class)->name('batch.delete');

                Route::prefix('rel')->name('rel.')->group(function () {
                    Route::prefix('{pageBlock}')->group(function () {
                        Route::get('/', [PageBlockController::class, 'view'])->name('view');
                        Route::patch('/update', [PageBlockController::class, 'update'])->name('update');
                        Route::delete('/delete', [PageBlockController::class, 'destroy'])->name('delete');
                        Route::prefix('reorder')->name('reorder.')->group(function () {
                            Route::post('/', PageBlockReorderController::class)->name('reorder');
                        });
                        Route::prefix('role')->name('role.')->group(function () {
                            Route::get('/', [PageBlockRoleController::class, 'index'])->name('index');
                            Route::prefix('{role}')->group(function () {
                                Route::post('/create', [PageBlockRoleController::class, 'create'])->name('create');
                                Route::delete('/delete', [PageBlockRoleController::class, 'destroy'])->name('delete');
                            });
                        });
                        Route::prefix('sidebar')->name('sidebar.')->group(function () {
                            Route::get('/', [PageBlockSidebarController::class, 'index'])->name('index');
                            Route::prefix('{sidebar}')->group(function () {
                                Route::post('/create', [PageBlockSidebarController::class, 'create'])->name('create');
                            });

                            Route::prefix('rel')->name('rel.')->group(function () {
                                Route::prefix('{pageBlockSidebar}')->group(function () {
                                    Route::delete('/delete', [PageBlockSidebarController::class, 'destroy'])->name('delete');
                                    Route::prefix('reorder')->name('reorder.')->group(function () {
                                        Route::post('/', PageBlockSidebarReorderController::class)->name('reorder');
                                    });
                                });
                            });
                        });
                    });
                });
                Route::prefix('{block}')->group(function () {
                    Route::post('/create', [PageBlockController::class, 'create'])->name('create');
                });
            });
        });
    });

    Route::prefix('site')->name('site.')->group(function () {
        Route::get('/', [SiteController::class, 'index'])->name('index');
        Route::post('/create', [SiteController::class, 'create'])->name('create');
        Route::prefix('{site}')->group(function () {
            Route::patch('/update', [SiteController::class, 'update'])->name('update');
            Route::delete('/delete', [SiteController::class, 'destroy'])->name('destroy');

            Route::prefix('token')->name('token.')->group(function () {
                Route::post('/create', [SiteTokenController::class, 'create'])->name('create');
                Route::prefix('{personalAccessToken}')->group(function () {
                    Route::delete('/delete', [SiteTokenController::class, 'destroy'])->name('destroy');
                });
            });
        });
    });
    Route::prefix('app-menu')->name('app_menu.')->group(function () {
        Route::post('/create', [AppMenuController::class, 'create'])->name('create');
        Route::prefix('{appMenu}')->group(function () {
            Route::patch('/update', [AppMenuController::class, 'update'])->name('update');
            Route::delete('/delete', [AppMenuController::class, 'destroy'])->name('delete');
            Route::prefix('item')->name('item.')->group(function () {
                Route::post('/create', [AppMenuItemController::class, 'create'])->name('create');
                Route::prefix('{appMenuItem}')->group(function () {
                    Route::patch('/update', [AppMenuItemController::class, 'update'])->name('update');
                    Route::delete('/delete', [AppMenuItemController::class, 'destroy'])->name('delete');
                });
            });
        });
    });
    Route::prefix('enum')->name('enum.')->group(function () {
        Route::get('/menu/item/type', MenuItemTypeController::class)->name('menu.item.type');
        Route::prefix('pagination')->name('pagination.')->group(function () {
            Route::get('/type', PaginationTypeController::class)->name('type');
            Route::get('/scroll/type', PaginationScrollTypeController::class)->name('scroll.type');
        });
        Route::prefix('link')->name('link.')->group(function () {
            Route::get('/target', LinkTargetController::class)->name('target');
        });
    });
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::post('/create', [MenuController::class, 'create'])->name('create');
        Route::get('/item/type', MenuItemTypeController::class)->name('item.type');
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::delete('/delete', MenuBulkDeleteController::class)->name('delete');
        });
        Route::prefix('{menu}')->group(function () {
            Route::patch('/update', [MenuController::class, 'update'])->name('update');
            Route::delete('/delete', [MenuController::class, 'destroy'])->name('delete');
            Route::prefix('role')->name('role.')->group(function () {
                Route::get('/', [MenuRoleController::class, 'index'])->name('index');
                Route::prefix('{role}')->group(function () {
                    Route::post('/create', [MenuRoleController::class, 'create'])->name('create');
                    Route::delete('/delete', [MenuRoleController::class, 'destroy'])->name('delete');
                });
            });
            Route::prefix('item')->name('item.')->group(function () {
                Route::get('/', [MenuItemController::class, 'index'])->name('index');
                Route::post('/create', [MenuItemController::class, 'create'])->name('create');
                Route::prefix('{menuItem}')->group(function () {
                    Route::get('/', [MenuItemController::class, 'view'])->name('view');
                    Route::patch('/update', [MenuItemController::class, 'update'])->name('update');
                    Route::delete('/delete', [MenuItemController::class, 'destroy'])->name('delete');
                    Route::prefix('reorder')->name('reorder.')->group(function () {
                        Route::post('/', MenuItemReorderController::class)->name('update');
                    });
                    Route::prefix('role')->name('role.')->group(function () {
                        Route::get('/', [MenuItemRoleController::class, 'index'])->name('index');
                        Route::prefix('{role}')->group(function () {
                            Route::post('/create', [MenuItemRoleController::class, 'create'])->name('create');
                            Route::delete('/delete', [MenuItemRoleController::class, 'destroy'])->name('delete');
                        });
                    });
                    Route::prefix('menu')->name('menu.')->group(function () {
                        Route::get('/', [MenuItemMenuController::class, 'index'])->name('index');
                        Route::prefix('{menuChild}')->group(function () {
                            Route::post('/create', [MenuItemMenuController::class, 'create'])->name('create');
                        });
                        Route::prefix('rel')->name('rel')->group(function () {
                            Route::prefix('{menuItemMenu}')->group(function () {
                                Route::delete('/delete', [MenuItemMenuController::class, 'destroy'])->name('delete');
                                Route::prefix('reorder')->name('reorder.')->group(function () {
                                    Route::post('/', MenuItemMenuReorderController::class)->name('update');
                                });
                            });
                        });
                    });
                });
            });
        });
    });
    Route::prefix('sidebar')->name('sidebar.')->group(function () {
        Route::get('/', [SidebarController::class, 'index'])->name('index');
        Route::post('/create', [SidebarController::class, 'create'])->name('create');
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::delete('/delete', SidebarBulkDeleteController::class)->name('delete');
        });
        Route::prefix('{sidebar}')->group(function () {
            Route::get('/', [SidebarController::class, 'view'])->name('view');
            Route::patch('/update', [SidebarController::class, 'update'])->name('update');
            Route::delete('/delete', [SidebarController::class, 'destroy'])->name('delete');
            Route::prefix('role')->name('role.')->group(function () {
                Route::get('/', [SidebarRoleController::class, 'index'])->name('index');
                Route::prefix('{role}')->group(function () {
                    Route::post('/create', [SidebarRoleController::class, 'create'])->name('create');
                    Route::delete('/delete', [SidebarRoleController::class, 'destroy'])->name('delete');
                });
            });
            Route::prefix('widget')->name('widget.')->group(function () {
                Route::get('/', [SidebarWidgetController::class, 'index'])->name('index');
                Route::prefix('rel')->name('rel')->group(function () {
                    Route::prefix('{sidebarWidget}')->group(function () {
                        Route::get('/', [SidebarWidgetController::class, 'view'])->name('view');
                        Route::patch('/update', [SidebarWidgetController::class, 'update'])->name('update');
                        Route::delete('/delete', [SidebarWidgetController::class, 'destroy'])->name('delete');
                        Route::prefix('reorder')->name('reorder.')->group(function () {
                            Route::post('/', SidebarWidgetReorderController::class)->name('reorder');
                        });
                        Route::prefix('role')->name('role.')->group(function () {
                            Route::get('/', [SidebarWidgetRoleController::class, 'index'])->name('index');
                            Route::prefix('{role}')->group(function () {
                                Route::post('/create', [SidebarWidgetRoleController::class, 'create'])->name('create');
                                Route::delete('/delete', [SidebarWidgetRoleController::class, 'destroy'])->name('delete');
                            });
                        });
                    });
                });
                Route::prefix('{widget}')->group(function () {
                    Route::post('/create', [SidebarWidgetController::class, 'create'])->name('create');
                });
            });
        });
    });
    Route::prefix('widget')->name('widget.')->group(function () {
        Route::get('/', [WidgetController::class, 'index'])->name('index');
        Route::post('/create', [WidgetController::class, 'create'])->name('create');
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::delete('/delete', WidgetBulkDeleteController::class)->name('delete');
        });
        Route::prefix('{widget}')->group(function () {
            Route::get('/', [WidgetController::class, 'view'])->name('view');
            Route::patch('/update', [WidgetController::class, 'update'])->name('update');
            Route::delete('/delete', [WidgetController::class, 'destroy'])->name('delete');
            Route::prefix('role')->name('role.')->group(function () {
                Route::get('/', [WidgetRoleController::class, 'index'])->name('index');
                Route::prefix('{role}')->group(function () {
                    Route::post('/create', [WidgetRoleController::class, 'create'])->name('create');
                    Route::delete('/delete', [WidgetRoleController::class, 'destroy'])->name('delete');
                });
            });
        });
    });
});
