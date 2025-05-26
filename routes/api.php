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
use App\Http\Controllers\Api\Brand\BrandController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Color\ColorController;
use App\Http\Controllers\Api\Listing\InitialiseListingController;
use App\Http\Controllers\Api\Listing\Brand\ListingBrandController;
use App\Http\Controllers\Api\Listing\Category\ListingCategoryController;
use App\Http\Controllers\Api\Listing\Color\ListingColorController;
use App\Http\Controllers\Api\Listing\ListingController;
use App\Http\Controllers\Api\Listing\Media\ListingMediaController;
use App\Http\Controllers\Api\Listing\ProductType\ListingProductTypeController;
use App\Http\Controllers\Api\Listing\ListingPublicController;
use App\Http\Controllers\Api\ProductType\ProductTypeController;
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
use App\Http\Controllers\Api\Feature\FeatureController;
use App\Http\Controllers\Api\Listing\Feature\ListingFeatureController;
use App\Http\Controllers\Api\Listing\Follow\ListingFollowController;
use App\Http\Controllers\Api\Listing\Price\ListingPriceController;
use App\Http\Controllers\Api\Listing\Review\ListingReviewController;
use App\Http\Controllers\Api\Listing\Type\ListingTypeController;
use App\Http\Controllers\Api\Locale\AddressController;
use App\Http\Controllers\Api\Locale\BulkCountryController;
use App\Http\Controllers\Api\Locale\BulkCurrencyController;
use App\Http\Controllers\Api\Review\ReviewController;
use App\Http\Controllers\Api\Menu\MenuBulkDeleteController;
use App\Http\Controllers\Api\Menu\MenuItemMenuController;
use App\Http\Controllers\Api\Menu\MenuItemMenuReorderController;
use App\Http\Controllers\Api\Menu\MenuItemReorderController;
use App\Http\Controllers\Api\Menu\MenuItemRoleController;
use App\Http\Controllers\Api\Menu\MenuRoleController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Order\Item\OrderItemController;
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
use App\Http\Controllers\Api\PaymentGateway\PaymentGatewayController;
use App\Http\Controllers\Api\PaymentMethod\PaymentMethodController;
use App\Http\Controllers\Api\Site\SiteController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\Price\PriceController;
use App\Http\Controllers\Api\Price\PriceTypeController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\Sidebar\SidebarBulkDeleteController;
use App\Http\Controllers\Api\Sidebar\SidebarController;
use App\Http\Controllers\Api\Sidebar\SidebarWidgetReorderController;
use App\Http\Controllers\Api\Sidebar\SidebarWidgetRoleController;
use App\Http\Controllers\Api\Site\SiteTokenController;
use App\Http\Controllers\Api\Site\Setting\SiteSettingController;
use App\Http\Controllers\Api\Tools\FileSystemController;
use App\Http\Controllers\Api\Transaction\Transaction\TransactionController;
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
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [SiteSettingController::class, 'view'])->name('view');
            });
        });
    });


    Route::prefix('listing')->name('listing.')->group(function () {
        Route::get('/', [ListingPublicController::class, 'index'])->name('index');
        Route::prefix('{listing}')->name('item.')->group(function () {
            Route::get('/', [ListingController::class, 'view'])->name('fetch');
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
    Route::get('/review', [ReviewController::class, 'index'])->name('review.index');
    Route::prefix('locale')->name('locale.')->group(function () {
        Route::get('/currency', [CurrencyController::class, 'index'])->name('currency.index');
        Route::get('/country', [CountryController::class, 'index'])->name('country.index');
        Route::prefix('address')->name('address.')->group(function () {
            Route::get('/', [AddressController::class, 'index'])->name('index');
            Route::post('/create', [AddressController::class, 'create'])->name('create');
            Route::get('/{address}', [AddressController::class, 'view'])->name('view');
            Route::patch('/{address}/update', [AddressController::class, 'update'])->name('update');
            Route::delete('/{address}/delete', [AddressController::class, 'destroy'])->name('delete');
        });
    });
    Route::get('/price-type', [PriceTypeController::class, 'index'])->name('price-type.index');
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

    Route::prefix('order')->name('order.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::post('/create', [OrderController::class, 'create'])->name('create');
        Route::get('/{order}', [OrderController::class, 'view'])->name('view');
        Route::prefix('{order}')->group(function () {
            Route::patch('/update', [OrderController::class, 'update'])->name('update');
            Route::delete('/delete', [OrderController::class, 'destroy'])->name('delete');
            Route::prefix('item')->name('item.')->group(function () {
                Route::get('/', [OrderItemController::class, 'index'])->name('index');
                Route::post('/create', [OrderItemController::class, 'create'])->name('create');
                Route::prefix('{orderItem}')->group(function () {
                    Route::get('/', [OrderItemController::class, 'view'])->name('view');
                    Route::patch('/update', [OrderItemController::class, 'update'])->name('update');
                    Route::delete('/delete', [OrderItemController::class, 'destroy'])->name('delete');
                });
            });
        });
    });
    Route::prefix('transaction')->name('transaction.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::post('/create', [TransactionController::class, 'create'])->name('create');
        Route::get('/{transaction}', [TransactionController::class, 'view'])->name('view');
        Route::patch('/{transaction}/update', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{transaction}/delete', [TransactionController::class, 'destroy'])->name('delete');
    });
    Route::prefix('listing')->name('listing.')->group(function () {
        Route::post('/create', [ListingController::class, 'create'])->name('create');
        Route::get('/initialize', InitialiseListingController::class)->name('initialize');

        Route::prefix('{listing?}')->group(function () {
            Route::patch('/update', [ListingController::class, 'update'])->name('update');
            Route::delete('/delete', [ListingController::class, 'destroy'])->name('delete');

            Route::prefix('price')->name('price.')->group(function () {
                Route::get('/', [ListingPriceController::class, 'index'])->name('index');
                Route::post('/create', [ListingPriceController::class, 'create'])->name('create');
                Route::get('/{price}', [ListingPriceController::class, 'view'])->name('view');
                Route::patch('/{price}/update', [ListingPriceController::class, 'update'])->name('update');
                Route::delete('/{price}/delete', [ListingPriceController::class, 'destroy'])->name('delete');
            });

            Route::prefix('feature')->name('feature.')->group(function () {
                Route::get('/', [ListingFeatureController::class, 'index'])->name('index');
                Route::post('/{feature}/create', [ListingFeatureController::class, 'create'])->name('create');
                Route::delete('/{feature}/delete', [ListingFeatureController::class, 'destroy'])->name('delete');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::delete('/delete', BulkUserDeleteController::class)->name('delete');
                    Route::delete('/create', BulkUserDeleteController::class)->name('create');
                });
            });
            Route::prefix('follow')->name('follow.')->group(function () {
                Route::get('/', [ListingFollowController::class, 'index'])->name('index');
                Route::post('/create', [ListingFollowController::class, 'create'])->name('create');
                Route::delete('/{listingFollow}/delete', [ListingFollowController::class, 'destroy'])->name('delete');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::delete('/delete', BulkUserDeleteController::class)->name('delete');
                    Route::delete('/create', BulkUserDeleteController::class)->name('create');
                });
            });
            Route::prefix('review')->name('review.')->group(function () {
                Route::get('/', [ListingReviewController::class, 'index'])->name('index');
                Route::post('/{listingReview}/create', [ListingReviewController::class, 'create'])->name('create');
                Route::delete('/{listingReview}/delete', [ListingReviewController::class, 'destroy'])->name('delete');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::delete('/delete', BulkUserDeleteController::class)->name('delete');
                    Route::delete('/create', BulkUserDeleteController::class)->name('create');
                });
            });
            Route::prefix('category')->name('category.')->group(function () {
                Route::get('/', [ListingCategoryController::class, 'index'])->name('index');
                Route::post('/{category}/create', [ListingCategoryController::class, 'create'])->name('create');
                Route::delete('/{category}/delete', [ListingCategoryController::class, 'destroy'])->name('delete');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::delete('/delete', BulkUserDeleteController::class)->name('delete');
                    Route::delete('/create', BulkUserDeleteController::class)->name('create');
                });
            });
            Route::prefix('brand')->name('brand.')->group(function () {
                Route::get('/', [ListingBrandController::class, 'index'])->name('index');
                Route::post('/{brand}/create', [ListingBrandController::class, 'create'])->name('create');
                Route::delete('/{brand}/delete', [ListingBrandController::class, 'destroy'])->name('delete');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::delete('/delete', BulkUserDeleteController::class)->name('delete');
                    Route::delete('/create', BulkUserDeleteController::class)->name('create');
                });
            });
            Route::prefix('color')->name('color.')->group(function () {
                Route::get('/', [ListingColorController::class, 'index'])->name('index');
                Route::post('/{color}/create', [ListingColorController::class, 'create'])->name('create');
                Route::delete('/{color}/delete', [ListingColorController::class, 'destroy'])->name('delete');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::delete('/delete', BulkUserDeleteController::class)->name('delete');
                    Route::delete('/create', BulkUserDeleteController::class)->name('create');
                });
            });
            Route::prefix('product-type')->name('product_type.')->group(function () {
                Route::get('/', [ListingProductTypeController::class, 'index'])->name('index');
                Route::post('/{productType}/create', [ListingProductTypeController::class, 'create'])->name('create');
                Route::delete('/{productType}/delete', [ListingProductTypeController::class, 'destroy'])->name('delete');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::delete('/delete', BulkUserDeleteController::class)->name('delete');
                    Route::delete('/create', BulkUserDeleteController::class)->name('create');
                });
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
    Route::prefix('payment-method')->name('payment-method.')->group(function () {
        Route::get('/', [PaymentMethodController::class, 'index'])->name('index');
        Route::post('/create', [PaymentMethodController::class, 'create'])->name('create');
        Route::get('/{paymentMethod}', [PaymentMethodController::class, 'view'])->name('view');
        Route::patch('/{paymentMethod}/update', [PaymentMethodController::class, 'update'])->name('update');
        Route::delete('/{paymentMethod}/delete', [PaymentMethodController::class, 'destroy'])->name('delete');
    });
    Route::prefix('payment-gateway')->name('payment-gateway.')->group(function () {
        Route::get('/', [PaymentGatewayController::class, 'index'])->name('index');
        Route::post('/create', [PaymentGatewayController::class, 'create'])->name('create');
        Route::get('/{paymentGateway}', [PaymentGatewayController::class, 'view'])->name('view');
        Route::patch('/{paymentGateway}/update', [PaymentGatewayController::class, 'update'])->name('update');
        Route::delete('/{paymentGateway}/delete', [PaymentGatewayController::class, 'destroy'])->name('delete');
    });
    Route::prefix('price')->name('price.')->group(function () {
        Route::post('/create', [PriceController::class, 'create'])->name('create');
        Route::get('/{price}', [PriceController::class, 'view'])->name('view');
        Route::patch('/{price}/update', [PriceController::class, 'update'])->name('update');
        Route::delete('/{price}/delete', [PriceController::class, 'destroy'])->name('delete');
    });
    Route::prefix('price-type')->name('price-type.')->group(function () {
        Route::post('/create', [PriceTypeController::class, 'create'])->name('create');
        Route::get('/{priceType}', [PriceTypeController::class, 'view'])->name('view');
        Route::patch('/{priceType}/update', [PriceTypeController::class, 'update'])->name('update');
        Route::delete('/{priceType}/delete', [PriceTypeController::class, 'destroy'])->name('delete');
    });

    Route::prefix('category')->name('category.')->group(function () {
        Route::post('/create', [CategoryController::class, 'create'])->name('create');
        Route::patch('/{category}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}/delete', [CategoryController::class, 'destroy'])->name('delete');
    });
    Route::prefix('brand')->name('brand.')->group(function () {
        Route::post('/create', [BrandController::class, 'create'])->name('create');
        Route::patch('/{brand}/update', [BrandController::class, 'update'])->name('update');
        Route::delete('/{brand}/delete', [BrandController::class, 'destroy'])->name('delete');
    });
    Route::prefix('color')->name('color.')->group(function () {
        Route::post('/create', [ColorController::class, 'create'])->name('create');
        Route::patch('/{color}/update', [ColorController::class, 'update'])->name('update');
        Route::delete('/{color}/delete', [ColorController::class, 'destroy'])->name('delete');
    });
    Route::prefix('product-type')->name('product_type.')->group(function () {
        Route::post('/create', [ProductTypeController::class, 'create'])->name('create');
        Route::patch('/{productType}/update', [ProductTypeController::class, 'update'])->name('update');
        Route::delete('/{productType}/delete', [ProductTypeController::class, 'destroy'])->name('delete');
    });
    Route::prefix('feature')->name('feature.')->group(function () {
        Route::post('/create', [FeatureController::class, 'create'])->name('create');
        Route::patch('/{feature}/update', [FeatureController::class, 'update'])->name('update');
        Route::delete('/{feature}/delete', [FeatureController::class, 'destroy'])->name('delete');
    });
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
            Route::post('/create', [CountryController::class, 'create'])->name('create');
            Route::post('/create/batch', [BulkCountryController::class, 'create'])->name('create_batch');
            Route::patch('/{country}/update', [CountryController::class, 'update'])->name('update');
            Route::delete('/{country}/delete', [CountryController::class, 'destroy'])->name('delete');
        });
        Route::prefix('currency')->name('currency.')->group(function () {
            Route::post('/create', [CurrencyController::class, 'create'])->name('create');
            Route::post('/create/batch', [BulkCurrencyController::class, 'create'])->name('create_batch');
            Route::patch('/{currency}/update', [CurrencyController::class, 'update'])->name('update');
            Route::delete('/{currency}/delete', [CurrencyController::class, 'destroy'])->name('delete');
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

            Route::prefix('settings')->name('settings.')->group(function () {
                Route::patch('/update', [SiteSettingController::class, 'update'])->name('update');
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
