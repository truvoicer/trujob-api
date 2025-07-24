<?php

use App\Http\Controllers\Api\Auth\ApiTokenController;
use App\Http\Controllers\Api\Auth\Session\SessionApiTokenController;
use App\Http\Controllers\Api\Auth\AuthLoginController;
use App\Http\Controllers\Api\Auth\AuthPasswordResetConfirmationController;
use App\Http\Controllers\Api\Auth\AuthPasswordResetController;
use App\Http\Controllers\Api\Auth\AuthPasswordResetTokenCheckController;
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
use App\Http\Controllers\Api\Discount\DiscountableTypeController;
use App\Http\Controllers\Api\Discount\DiscountAmountTypeController;
use App\Http\Controllers\Api\Discount\DiscountController;
use App\Http\Controllers\Api\Discount\DiscountScopeController;
use App\Http\Controllers\Api\Discount\DiscountSetAsDefaultController;
use App\Http\Controllers\Api\Discount\DiscountTypeController;
use App\Http\Controllers\Api\Discount\UserDiscountUsageController;
use App\Http\Controllers\Api\Product\InitialiseProductController;
use App\Http\Controllers\Api\Product\Brand\ProductBrandController;
use App\Http\Controllers\Api\Product\Category\CategoryProductController;
use App\Http\Controllers\Api\Product\Color\ProductColorController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Product\ProductPublicController;
use App\Http\Controllers\Api\Product\UserProductController;
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
use App\Http\Controllers\Api\Product\Feature\ProductFeatureController;
use App\Http\Controllers\Api\Product\Follow\ProductFollowController;
use App\Http\Controllers\Api\Product\Price\ProductPriceController;
use App\Http\Controllers\Api\Product\Review\ProductReviewController;
use App\Http\Controllers\Api\Product\Type\ProductTypeController;
use App\Http\Controllers\Api\Locale\AddressController;
use App\Http\Controllers\Api\Locale\BulkCountryController;
use App\Http\Controllers\Api\Locale\BulkCurrencyController;
use App\Http\Controllers\Api\Locale\BulkRegionController;
use App\Http\Controllers\Api\Review\ReviewController;
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
use App\Http\Controllers\Api\PaymentGateway\PaymentGatewayController;
use App\Http\Controllers\Api\PaymentMethod\PaymentMethodController;
use App\Http\Controllers\Api\Site\SiteController;
use App\Http\Controllers\Api\Permission\PermissionController;
use App\Http\Controllers\Api\Price\PriceController;
use App\Http\Controllers\Api\Price\Discount\PriceDiscountController;
use App\Http\Controllers\Api\Price\TaxRate\PriceTaxRateController;
use App\Http\Controllers\Api\Locale\RegionController;
use App\Http\Controllers\Api\PaymentGateway\AvailableSitePaymentGatewayController;
use App\Http\Controllers\Api\PaymentGateway\PaymentGatewayEnvironmentController;
use App\Http\Controllers\Api\PaymentGateway\SitePaymentGatewayController;
use App\Http\Controllers\Api\Permission\BulkPermissionController;
use App\Http\Controllers\Api\Price\BulkPriceController;
use App\Http\Controllers\Api\Price\Discount\BulkPriceDiscountController;
use App\Http\Controllers\Api\Price\TaxRate\BulkPriceTaxRateController;
use App\Http\Controllers\Api\Price\Type\PriceTypeController;
use App\Http\Controllers\Api\Product\Brand\BulkProductBrandController;
use App\Http\Controllers\Api\Product\Category\BulkCategoryProductController;
use App\Http\Controllers\Api\Product\Color\BulkProductColorController;
use App\Http\Controllers\Api\Product\Feature\BulkProductFeatureController;
use App\Http\Controllers\Api\Product\Follow\BulkProductFollowController;
use App\Http\Controllers\Api\Product\Media\ProductMediaController;
use App\Http\Controllers\Api\Product\Price\BulkProductPriceController;
use App\Http\Controllers\Api\Product\Price\ProductPriceTypeController;
use App\Http\Controllers\Api\Product\ProductCategory\BulkProductCategoryController;
use App\Http\Controllers\Api\Product\ProductCategory\ProductCategoryController;
use App\Http\Controllers\Api\Product\ProductProductCategory\BulkProductProductCategoryController;
use App\Http\Controllers\Api\Product\ProductProductCategory\ProductProductCategoryController;
use App\Http\Controllers\Api\Product\ProductSkuController;
use App\Http\Controllers\Api\Product\ProductUnitController;
use App\Http\Controllers\Api\Product\ProductWeightUnitController;
use App\Http\Controllers\Api\Product\Review\BulkProductReviewController;
use App\Http\Controllers\Api\Product\Shipping\Method\BulkProductShippingMethodController;
use App\Http\Controllers\Api\Product\Shipping\Method\ProductShippingMethodController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\Shipping\Method\Discount\BulkShippingMethodDiscountController;
use App\Http\Controllers\Api\Shipping\Method\Discount\ShippingMethodDiscountController;
use App\Http\Controllers\Api\Shipping\Method\Rate\ShippingMethodRateController;
use App\Http\Controllers\Api\Shipping\Method\Restriction\ShippingMethodRestrictionController;
use App\Http\Controllers\Api\Shipping\Method\Restriction\ShippingRestrictionActionController;
use App\Http\Controllers\Api\Shipping\Method\Restriction\ShippingRestrictionTypeController;
use App\Http\Controllers\Api\Shipping\Zone\Country\BulkShippingZoneCountryController;
use App\Http\Controllers\Api\Shipping\Method\ShippingMethodController;
use App\Http\Controllers\Api\Shipping\Zone\ShippingZoneController;
use App\Http\Controllers\Api\Shipping\Zone\Country\ShippingZoneCountryController;
use App\Http\Controllers\Api\Shipping\Zone\Discount\BulkShippingZoneDiscountController;
use App\Http\Controllers\Api\Shipping\Zone\Discount\ShippingZoneDiscountController;
use App\Http\Controllers\Api\Sidebar\SidebarBulkDeleteController;
use App\Http\Controllers\Api\Sidebar\SidebarController;
use App\Http\Controllers\Api\Sidebar\SidebarWidgetReorderController;
use App\Http\Controllers\Api\Sidebar\SidebarWidgetRoleController;
use App\Http\Controllers\Api\Site\SiteTokenController;
use App\Http\Controllers\Api\Site\Setting\SiteSettingController;
use App\Http\Controllers\Api\Shipping\ShippingRateTypeController;
use App\Http\Controllers\Api\Shipping\ShippingUnitController;
use App\Http\Controllers\Api\Shipping\ShippingWeightUnitController;
use App\Http\Controllers\Api\Shipping\Zone\ShippingZoneableTypeController;
use App\Http\Controllers\Api\Subscription\SubscriptionIntervalUnitController;
use App\Http\Controllers\Api\Subscription\SubscriptionSetupFeeFailureActionController;
use App\Http\Controllers\Api\Subscription\SubscriptionTenureTypeController;
use App\Http\Controllers\Api\Tax\TaxRateAmountTypeController;
use App\Http\Controllers\Api\Tax\TaxRateController;
use App\Http\Controllers\Api\Tax\TaxRateScopeController;
use App\Http\Controllers\Api\Tax\TaxRateSetAsDefaultController;
use App\Http\Controllers\Api\Tax\TaxRateTypeController;
use App\Http\Controllers\Api\Tools\FileSystemController;
use App\Http\Controllers\Api\User\RoleController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\User\UserProfileController;
use App\Http\Controllers\Api\User\UserSellerController;
use App\Http\Controllers\Api\User\UserSettingController;
use App\Http\Controllers\Api\Widget\WidgetBulkDeleteController;
use App\Http\Controllers\Api\Widget\WidgetController;
use App\Http\Controllers\Api\Widget\WidgetRoleController;
use App\Http\Middleware\AppPublic;
use App\Http\Middleware\AuthenticateSiteRequest;
use App\Http\Middleware\AuthenticateSiteUserRequest;
use Illuminate\Support\Facades\Route;


Route::middleware(AppPublic::class)->group(function () {});


Route::middleware([
    'auth:sanctum',
    'ability:api:admin,api:superuser,api:super_admin,api:user,api:app_user',
    AuthenticateSiteUserRequest::class,
])->group(function () {
    Route::prefix('site')->name('site.')->group(function () {
        Route::prefix('payment-gateway')->name('payment-gateway.')->group(function () {
            Route::get('/', [SitePaymentGatewayController::class, 'index'])->name('index');
            Route::get('/available', [AvailableSitePaymentGatewayController::class, 'index'])->name('available.index');
            Route::prefix('{paymentGateway}')->group(function () {
                Route::get('/', [SitePaymentGatewayController::class, 'show'])->name('show');
                Route::patch('/update', [SitePaymentGatewayController::class, 'update'])->name('update');
                Route::delete('/destroy', [SitePaymentGatewayController::class, 'destroy'])->name('destroy');
            });
        });
    });
    Route::prefix('session')->name('session.')->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/show', [SessionUserController::class, 'show'])->name('show');
            Route::patch('/update', [SessionUserController::class, 'update'])->name('update');
            Route::prefix('api-token')->name('api-token.')->group(function () {
                Route::get('/', [SessionApiTokenController::class, 'index'])->name('index');
                Route::get('/show', [SessionApiTokenController::class, 'show'])->name('show');
                Route::get('/store', [SessionApiTokenController::class, 'store'])->name('store');
                Route::delete('/destroy', [SessionApiTokenController::class, 'destroy'])->name('destroy');
            });
        });
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::patch('/update', [UserProfileController::class, 'update'])->name('update');
        });

        Route::prefix('setting')->name('setting.')->group(function () {
            Route::get('/', [UserSettingController::class, 'show'])->name('show');
            Route::patch('/update', [UserSettingController::class, 'update'])->name('update');
        });
        Route::prefix('product')->name('product.')->group(function () {
            Route::get('/', [UserProductController::class, 'index'])->name('index');
            Route::get('/{product?}', [UserProductController::class, 'show'])->name('edit');
        });

        Route::prefix('{user}')->group(function () {
            Route::prefix('discount')->name('discount')->group(function () {
                Route::prefix('{discount}')->group(function () {
                    Route::prefix('usage')->name('usage')->group(function () {
                        Route::post('/store', [UserDiscountUsageController::class, 'store'])->name('store');
                        Route::get('/', [UserDiscountUsageController::class, 'show'])->name('show');
                        Route::patch('/update', [UserDiscountUsageController::class, 'update'])->name('update');
                        Route::delete('/destroy', [UserDiscountUsageController::class, 'destroy'])->name('destroy');
                    });
                });
            });
        });
    });

    Route::prefix('firebase')->name('firebase.')->group(function () {
        Route::prefix('device')->name('device.')->group(function () {
            Route::post('/register', [FirebaseDeviceController::class, 'registerFirebaseDevice'])->name('register');
        });
    });

    Route::prefix('discount')->name('discount')->group(function () {
        Route::get('/', [DiscountController::class, 'index'])->name('index');
        Route::post('/store', [DiscountController::class, 'store'])->name('store');
        Route::prefix('{discount}')->group(function () {
            Route::get('/', [DiscountController::class, 'show'])->name('show');
            Route::patch('/update', [DiscountController::class, 'update'])->name('update');
            Route::delete('/destroy', [DiscountController::class, 'destroy'])->name('destroy');
            Route::post('/set-default', [DiscountSetAsDefaultController::class, 'store'])->name('set-default');
            Route::delete('/unset-default', [DiscountSetAsDefaultController::class, 'destroy'])->name('unset-default');
        });
    });

    include __DIR__ . '/api/order/order.php';

    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::prefix('tenure-type')->name('tenure-type.')->group(function () {
            Route::get('/', [SubscriptionTenureTypeController::class, 'index'])->name('index');
        });

        Route::prefix('interval-unit')->name('interval-unit.')->group(function () {
            Route::get('/', [SubscriptionIntervalUnitController::class, 'index'])->name('index');
        });
        Route::prefix('setup-fee-failure-action')->name('setup-fee-failure-action.')->group(function () {
            Route::get('/', [SubscriptionSetupFeeFailureActionController::class, 'index'])->name('index');
        });
    });
    Route::prefix('price')->name('price.')->group(function () {
        Route::prefix('{price}')->group(function () {
            Route::prefix('tax-rate')->name('tax-rate.')->group(function () {
                Route::get('/', [PriceTaxRateController::class, 'index'])->name('index');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::post('/store', [BulkPriceTaxRateController::class, 'store'])->name('store');
                    Route::delete('/destroy', [BulkPriceTaxRateController::class, 'destroy'])->name('destroy');
                });
                Route::prefix('{taxRate}')->group(function () {
                    Route::get('/', [PriceTaxRateController::class, 'show'])->name('show');
                    Route::post('/store', [PriceTaxRateController::class, 'store'])->name('store');
                    Route::delete('/destroy', [PriceTaxRateController::class, 'destroy'])->name('destroy');
                });
            });

            Route::prefix('discount')->name('discount')->group(function () {
                Route::get('/', [PriceDiscountController::class, 'index'])->name('index');
                Route::post('/store', [PriceDiscountController::class, 'store'])->name('store');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::post('/store', [BulkPriceDiscountController::class, 'store'])->name('store');
                    Route::delete('/destroy', [BulkPriceDiscountController::class, 'destroy'])->name('destroy');
                });
                Route::prefix('{discount}')->group(function () {
                    Route::get('/', [PriceDiscountController::class, 'show'])->name('show');
                    Route::patch('/update', [PriceDiscountController::class, 'update'])->name('update');
                    Route::delete('/destroy', [PriceDiscountController::class, 'destroy'])->name('destroy');
                });
            });
        });
    });
    Route::prefix('product-category')->name('product-category.')->group(function () {
        Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::post('/store', [BulkProductCategoryController::class, 'store'])->name('store');
            Route::delete('/destroy', [BulkProductCategoryController::class, 'destroy'])->name('destroy');
        });
        Route::post('/{productCategory}/store', [ProductCategoryController::class, 'store'])->name('store');
        Route::delete('/{productCategory}/destroy', [ProductCategoryController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('product')->name('product.')->group(function () {
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::get('/initialize', InitialiseProductController::class)->name('initialize');
        Route::get('/unit', [ProductUnitController::class, 'index'])->name('unit.index');
        Route::get('/weight-unit', [ProductWeightUnitController::class, 'index'])->name('weight-unit.index');

        Route::prefix('{product}')->group(function () {
            Route::get('/', [ProductController::class, 'show'])->name('show');
            Route::patch('/update', [ProductController::class, 'update'])->name('update');
            Route::delete('/destroy', [ProductController::class, 'destroy'])->name('destroy');
            Route::prefix('price-type')->name('price-type.')->group(function () {
                Route::get('/', [ProductPriceTypeController::class, 'index'])->name('index');
            });
            Route::patch('/sku/update', [ProductSkuController::class, 'update'])->name('sku.update');
            Route::prefix('shipping')->name('shipping.')->group(function () {
                Route::prefix('method')->name('method.')->group(function () {
                    Route::prefix('bulk')->name('bulk.')->group(function () {
                        Route::post('/store', [BulkProductShippingMethodController::class, 'store'])->name('store');
                        Route::delete('/destroy', [BulkProductShippingMethodController::class, 'destroy'])->name('destroy');
                    });
                    Route::get('/', [ProductShippingMethodController::class, 'index'])->name('index');
                    Route::prefix('{shippingMethod}')->group(function () {
                        Route::get('/', [ProductShippingMethodController::class, 'show'])->name('show');
                        Route::post('/store', [ProductShippingMethodController::class, 'store'])->name('store');
                        Route::delete('/destroy', [ProductShippingMethodController::class, 'destroy'])->name('destroy');
                    });
                });
            });

            Route::prefix('product-category')->name('product-type.')->group(function () {
                Route::get('/', [ProductProductCategoryController::class, 'index'])->name('index');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::post('/store', [BulkProductProductCategoryController::class, 'store'])->name('store');
                    Route::delete('/destroy', [BulkProductProductCategoryController::class, 'destroy'])->name('destroy');
                });
                Route::post('/{productCategory}/store', [ProductProductCategoryController::class, 'store'])->name('store');
                Route::delete('/{productCategory}/destroy', [ProductProductCategoryController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('price')->name('price.')->group(function () {
                Route::get('/', [ProductPriceController::class, 'index'])->name('index');
                Route::post('/store', [ProductPriceController::class, 'store'])->name('store');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::post('/store', [BulkProductPriceController::class, 'store'])->name('store');
                    Route::delete('/destroy', [BulkProductPriceController::class, 'destroy'])->name('destroy');
                });
                Route::prefix('{price}')->group(function () {
                    Route::get('/', [ProductPriceController::class, 'show'])->name('show');
                    Route::patch('/update', [ProductPriceController::class, 'update'])->name('update');
                    Route::delete('/destroy', [ProductPriceController::class, 'destroy'])->name('destroy');
                });
            });
            Route::prefix('feature')->name('feature.')->group(function () {
                Route::get('/', [ProductFeatureController::class, 'index'])->name('index');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::post('/store', [BulkProductFeatureController::class, 'store'])->name('store');
                    Route::delete('/destroy', [BulkProductFeatureController::class, 'destroy'])->name('destroy');
                });
                Route::prefix('{feature}')->group(function () {
                    Route::post('/store', [ProductFeatureController::class, 'store'])->name('store');
                    Route::delete('/destroy', [ProductFeatureController::class, 'destroy'])->name('destroy');
                });
            });
            Route::prefix('follow')->name('follow.')->group(function () {
                Route::get('/', [ProductFollowController::class, 'index'])->name('index');
                Route::post('/store', [ProductFollowController::class, 'store'])->name('store');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::post('/store', [BulkProductFollowController::class, 'store'])->name('store');
                    Route::delete('/destroy', [BulkProductFollowController::class, 'destroy'])->name('destroy');
                });
                Route::delete('/{productFollow}/destroy', [ProductFollowController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('review')->name('review.')->group(function () {
                Route::get('/', [ProductReviewController::class, 'index'])->name('index');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::post('/store', [BulkProductReviewController::class, 'store'])->name('store');
                    Route::delete('/destroy', [BulkProductReviewController::class, 'destroy'])->name('destroy');
                });
                Route::post('/{productReview}/store', [ProductReviewController::class, 'store'])->name('store');
                Route::delete('/{productReview}/destroy', [ProductReviewController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('category')->name('category.')->group(function () {
                Route::get('/', [CategoryProductController::class, 'index'])->name('index');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::post('/store', [BulkCategoryProductController::class, 'store'])->name('store');
                    Route::delete('/destroy', [BulkCategoryProductController::class, 'destroy'])->name('destroy');
                });
                Route::post('/{category}/store', [CategoryProductController::class, 'store'])->name('store');
                Route::delete('/{category}/destroy', [CategoryProductController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('brand')->name('brand.')->group(function () {
                Route::get('/', [ProductBrandController::class, 'index'])->name('index');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::post('/store', [BulkProductBrandController::class, 'store'])->name('store');
                    Route::delete('/destroy', [BulkProductBrandController::class, 'destroy'])->name('destroy');
                });
                Route::post('/{brand}/store', [ProductBrandController::class, 'store'])->name('store');
                Route::delete('/{brand}/destroy', [ProductBrandController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('color')->name('color.')->group(function () {
                Route::get('/', [ProductColorController::class, 'index'])->name('index');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::post('/store', [BulkProductColorController::class, 'store'])->name('store');
                    Route::delete('/destroy', [BulkProductColorController::class, 'destroy'])->name('destroy');
                });
                Route::post('/{color}/store', [ProductColorController::class, 'store'])->name('store');
                Route::delete('/{color}/destroy', [ProductColorController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('messaging-group')->name('message_group.')->group(function () {
                Route::post('/store', [MessagingGroupController::class, 'storeMessageGroup'])->name('store');
                Route::prefix('{messagingGroup}')->name('message_group.')->group(function () {
                    Route::delete('/destroy', [MessagingGroupController::class, 'deleteMessageGroup'])->name('destroy');
                    Route::prefix('message')->name('message.')->group(function () {
                        Route::post('/store', [MessagingGroupMessageController::class, 'storeMessage'])->name('store');
                        Route::prefix('/{messagingGroupMessage}')->group(function () {
                            Route::patch('/update', [MessagingGroupMessageController::class, 'updateMessage'])->name('update');
                            Route::delete('/destroy', [MessagingGroupMessageController::class, 'deleteMessage'])->name('destroy');
                        });
                    });
                });
            });
        });

        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/', [ProductMediaController::class, 'index'])->name('index');
            Route::post('/store', [ProductMediaController::class, 'store'])->name('store');
            Route::prefix('{productMedia?}')->name('item.')->group(function () {
                Route::get('/', [ProductMediaController::class, 'show'])->name('show');
                Route::patch('/update', [ProductMediaController::class, 'update'])->name('update');
                Route::delete('/destroy', [ProductMediaController::class, 'destroy'])->name('destroy');
            });
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
        Route::delete('/{notification}/destroy', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/destroy-all', [NotificationController::class, 'deleteAll'])->name('delete-all');
    });



    Route::prefix('shipping')->name('shipping.')->group(function () {
        Route::get('/unit', [ShippingUnitController::class, 'index'])->name('unit.index');
        Route::get('/weight-unit', [ShippingWeightUnitController::class, 'index'])->name('weight-unit.index');
        Route::prefix('rate')->name('rate.')->group(function () {
            Route::get('/type', [ShippingRateTypeController::class, 'index'])->name('type.index');
        });
        Route::prefix('restriction')->name('restriction.')->group(function () {
            Route::get('/action', [ShippingRestrictionActionController::class, 'index'])->name('action.index');
            Route::get('/type', [ShippingRestrictionTypeController::class, 'index'])->name('type.index');
        });
        Route::prefix('method')->name('method.')->group(function () {
            Route::get('/', [ShippingMethodController::class, 'index'])->name('index');
            Route::post('/store', [ShippingMethodController::class, 'store'])->name('store');
            Route::prefix('{shippingMethod}')->group(function () {
                Route::get('/', [ShippingMethodController::class, 'show'])->name('show');
                Route::patch('/update', [ShippingMethodController::class, 'update'])->name('update');
                Route::delete('/destroy', [ShippingMethodController::class, 'destroy'])->name('destroy');
                Route::prefix('discount')->name('discount.')->group(function () {
                    Route::get('/', [ShippingMethodDiscountController::class, 'index'])->name('index');
                    Route::prefix('bulk')->name('bulk.')->group(function () {
                        Route::post('/store', BulkShippingMethodDiscountController::class)->name('store');
                    });
                    Route::prefix('{discount}')->group(function () {
                        Route::post('/store', [ShippingMethodDiscountController::class, 'store'])->name('store');
                        Route::delete('/destroy', [ShippingMethodDiscountController::class, 'destroy'])->name('destroy');
                    });
                });

                Route::prefix('rate')->name('rate.')->group(function () {
                    Route::get('/', [ShippingMethodRateController::class, 'index'])->name('index');
                    Route::post('/store', [ShippingMethodRateController::class, 'store'])->name('store');
                    Route::prefix('{shippingRate}')->group(function () {
                        Route::get('/', [ShippingMethodRateController::class, 'show'])->name('show');
                        Route::patch('/update', [ShippingMethodRateController::class, 'update'])->name('update');
                        Route::delete('/destroy', [ShippingMethodRateController::class, 'destroy'])->name('destroy');
                    });
                });
                Route::prefix('restriction')->name('restriction.')->group(function () {
                    Route::get('/', [ShippingMethodRestrictionController::class, 'index'])->name('index');
                    Route::post('/store', [ShippingMethodRestrictionController::class, 'store'])->name('store');
                    Route::prefix('{shippingRestriction}')->group(function () {
                        Route::get('/', [ShippingMethodRestrictionController::class, 'show'])->name('show');
                        Route::patch('/update', [ShippingMethodRestrictionController::class, 'update'])->name('update');
                        Route::delete('/destroy', [ShippingMethodRestrictionController::class, 'destroy'])->name('destroy');
                    });
                });
            });
        });
        Route::prefix('zone')->name('zone.')->group(function () {
            Route::get('/', [ShippingZoneController::class, 'index'])->name('index');
            Route::post('/store', [ShippingZoneController::class, 'store'])->name('store');

            Route::get('/type', [ShippingZoneableTypeController::class, 'index'])->name('type.index');

            Route::prefix('{shippingZone}')->group(function () {
                Route::get('/', [ShippingZoneController::class, 'show'])->name('show');
                Route::patch('/update', [ShippingZoneController::class, 'update'])->name('update');
                Route::delete('/destroy', [ShippingZoneController::class, 'destroy'])->name('destroy');
                Route::prefix('country')->name('country.')->group(function () {
                    Route::get('/', [ShippingZoneCountryController::class, 'index'])->name('index');
                    Route::prefix('bulk')->name('bulk.')->group(function () {
                        Route::post('/store', BulkShippingZoneCountryController::class)->name('store');
                    });
                    Route::prefix('{country}')->group(function () {
                        Route::post('/store', [ShippingZoneCountryController::class, 'store'])->name('store');
                        Route::delete('/destroy', [ShippingZoneCountryController::class, 'destroy'])->name('destroy');
                    });
                });
                Route::prefix('discount')->name('discount.')->group(function () {
                    Route::get('/', [ShippingZoneDiscountController::class, 'index'])->name('index');
                    Route::prefix('bulk')->name('bulk.')->group(function () {
                        Route::post('/store', BulkShippingZoneDiscountController::class)->name('store');
                    });
                    Route::prefix('{discount}')->group(function () {
                        Route::post('/store', [ShippingZoneDiscountController::class, 'store'])->name('store');
                        Route::delete('/destroy', [ShippingZoneDiscountController::class, 'destroy'])->name('destroy');
                    });
                });
            });
        });
    });
});

Route::middleware([
    'auth:sanctum',
    'ability:api:admin,api:superuser,api:super_admin,api:site',
    AuthenticateSiteRequest::class
])->group(function () {
    Route::get('/reset-password/{token}', [AuthPasswordResetController::class, 'show'])->name('password.reset');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');

    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('/show', [AuthUserController::class, 'show'])->name('show');
        Route::post('/login', AuthLoginController::class)->name('login');
        Route::post('/register', AuthRegisterController::class)->name('register');
    });
    Route::prefix('site')->name('site.')->group(function () {
        Route::get('/page', [SitePageController::class, 'show'])->name('page.view');
        Route::prefix('{site:name}')->group(function () {
            Route::get('/', [SiteController::class, 'show'])->name('show');
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/', [SiteSettingController::class, 'show'])->name('show');
            });
        });
    });


    Route::prefix('public')->name('public.')->group(function () {
        Route::prefix('product')->name('product.')->group(function () {
            Route::get('/', [ProductPublicController::class, 'index'])->name('index');
            Route::prefix('{product}')->name('item.')->group(function () {
                Route::get('/', [ProductController::class, 'show'])->name('fetch');
            });
        });
    });

    Route::prefix('app-menu')->name('app_menu.')->group(function () {
        Route::prefix('{appMenu}')->group(function () {
            Route::get('/', [AppMenuController::class, 'show'])->name('show');
        });
    });
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::prefix('{menu}')->group(function () {
            Route::get('/', [MenuController::class, 'show'])->name('show');
        });
    });
});

Route::middleware([
    'auth:sanctum',
    'ability:api:admin,api:superuser,api:super_admin,api:site,api:user,api:app_user',
    AuthenticateSiteUserRequest::class,
])->group(function () {
    Route::prefix('payment-gateway')->name('payment-gateway.')->group(function () {
        Route::get('/environment', [PaymentGatewayEnvironmentController::class, 'index'])->name('environment.index');
    });
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::prefix('password')->name('password.')->group(function () {
            Route::prefix('reset')->name('reset.')->group(function () {
                Route::post('/token-check', AuthPasswordResetTokenCheckController::class)->name('token-check');
                Route::post('/request', [AuthPasswordResetController::class, 'store'])->name('request');
                Route::post('/confirmation', AuthPasswordResetConfirmationController::class)->name('confirmation');
            });
        });
    });
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/brand', [BrandController::class, 'index'])->name('brand.index');
    Route::get('/color', [ColorController::class, 'index'])->name('color.index');
    Route::get('/product-type', [ProductTypeController::class, 'index'])->name('product-type.index');
    Route::get('/feature', [FeatureController::class, 'index'])->name('feature.index');
    Route::get('/review', [ReviewController::class, 'index'])->name('review.index');
    Route::get('/price-type', [PriceTypeController::class, 'index'])->name('price-type.index');
    Route::prefix('discount')->name('discount.')->group(function () {
        Route::get('/type', DiscountTypeController::class)->name('type.index');
        Route::get('/discountable/type', DiscountableTypeController::class)->name('discountable.type.index');
        Route::get('/amount-type', DiscountAmountTypeController::class)->name('amount-type.index');
        Route::get('/scope', DiscountScopeController::class)->name('scope.index');
    });
    Route::prefix('locale')->name('locale.')->group(function () {
        Route::prefix('currency')->name('currency.')->group(function () {
            Route::get('/', [CurrencyController::class, 'index'])->name('index');
        });
        Route::get('/country', [CountryController::class, 'index'])->name('country.index');
        Route::get('/region', [RegionController::class, 'index'])->name('region.index');
        Route::prefix('address')->name('address.')->group(function () {
            Route::get('/', [AddressController::class, 'index'])->name('index');
            Route::post('/store', [AddressController::class, 'store'])->name('store');
            Route::get('/{address}', [AddressController::class, 'show'])->name('show');
            Route::patch('/{address}/update', [AddressController::class, 'update'])->name('update');
            Route::delete('/{address}/destroy', [AddressController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('region')->name('region.')->group(function () {
            Route::get('/', [RegionController::class, 'index'])->name('index');
            Route::post('/store', [RegionController::class, 'store'])->name('store');
            Route::get('/{region}', [RegionController::class, 'show'])->name('show');
            Route::patch('/{region}/update', [RegionController::class, 'update'])->name('update');
            Route::delete('/{region}/destroy', [RegionController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('tax-rate')->name('tax-rate.')->group(function () {
        Route::get('/', [TaxRateController::class, 'index'])->name('index');
        Route::post('/store', [TaxRateController::class, 'store'])->name('store');
        Route::prefix('type')->name('type.')->group(function () {
            Route::get('/', [TaxRateTypeController::class, 'index'])->name('index');
        });
        Route::prefix('scope')->name('scope.')->group(function () {
            Route::get('/', [TaxRateScopeController::class, 'index'])->name('index');
        });
        Route::prefix('amount-type')->name('amount-type.')->group(function () {
            Route::get('/', [TaxRateAmountTypeController::class, 'index'])->name('index');
        });
        Route::prefix('{taxRate}')->group(function () {
            Route::get('/', [TaxRateController::class, 'show'])->name('show');
            Route::patch('/update', [TaxRateController::class, 'update'])->name('update');
            Route::delete('/destroy', [TaxRateController::class, 'destroy'])->name('destroy');
            Route::post('/set-default', [TaxRateSetAsDefaultController::class, 'store'])->name('set-default');
            Route::delete('/unset-default', [TaxRateSetAsDefaultController::class, 'destroy'])->name('unset-default');
        });
    });
});

Route::middleware([
    'auth:sanctum',
    'ability:api:admin,api:superuser,api:super_admin,api:user',
    AuthenticateSiteUserRequest::class,
])->group(function () {
    Route::prefix('tools')->name('tools.')->group(function () {
        Route::prefix('filesystem')->name('filesystem.')->group(function () {
            Route::get('/list', [FileSystemController::class, 'getFiles'])->name('list');
            Route::get('/{file}', [FileSystemController::class, 'getSingleFile'])->name('detail');
            Route::prefix('{file}')->name('single.')->group(function () {
                Route::get('/download', [FileSystemController::class, 'downloadFile'])->name('download');
                Route::delete('/destroy', [FileSystemController::class, 'deleteFile'])->name('destroy');
            });
        });
    });
});

Route::middleware([
    'auth:sanctum',
    'ability:api:superuser,',
    AuthenticateSiteUserRequest::class,
])->group(function () {

    Route::prefix('permission')->name('permission.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/store', [PermissionController::class, 'store'])->name('store');
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::delete('/destroy', [BulkPermissionController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('{permission}')->group(function () {
            Route::get('/', [PermissionController::class, 'show'])->name('show');
            Route::patch('/update', [PermissionController::class, 'update'])->name('update');
            Route::delete('/destroy', [PermissionController::class, 'destroy'])->name('destroy');
        });
    });
});

Route::middleware([
    'auth:sanctum',
    'ability:api:admin,api:superuser,api:super_admin',
    AuthenticateSiteUserRequest::class,
])->group(function () {
    Route::prefix('payment-method')->name('payment-method.')->group(function () {
        Route::get('/', [PaymentMethodController::class, 'index'])->name('index');
        Route::post('/store', [PaymentMethodController::class, 'store'])->name('store');
        Route::get('/{paymentMethod}', [PaymentMethodController::class, 'show'])->name('show');
        Route::patch('/{paymentMethod}/update', [PaymentMethodController::class, 'update'])->name('update');
        Route::delete('/{paymentMethod}/destroy', [PaymentMethodController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('payment-gateway')->name('payment-gateway.')->group(function () {
        Route::get('/', [PaymentGatewayController::class, 'index'])->name('index');
        Route::post('/store', [PaymentGatewayController::class, 'store'])->name('store');
        Route::get('/{paymentGateway}', [PaymentGatewayController::class, 'show'])->name('show');
        Route::patch('/{paymentGateway}/update', [PaymentGatewayController::class, 'update'])->name('update');
        Route::delete('/{paymentGateway}/destroy', [PaymentGatewayController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('price')->name('price.')->group(function () {
        Route::post('/store', [PriceController::class, 'store'])->name('store');

        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::delete('/destroy', [BulkPriceController::class, 'destroy'])->name('destroy');
        });
        Route::get('/{price}', [PriceController::class, 'show'])->name('show');
        Route::patch('/{price}/update', [PriceController::class, 'update'])->name('update');
        Route::delete('/{price}/destroy', [PriceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('category')->name('category.')->group(function () {
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::patch('/{category}/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}/destroy', [CategoryController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('brand')->name('brand.')->group(function () {
        Route::post('/store', [BrandController::class, 'store'])->name('store');
        Route::patch('/{brand}/update', [BrandController::class, 'update'])->name('update');
        Route::delete('/{brand}/destroy', [BrandController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('color')->name('color.')->group(function () {
        Route::post('/store', [ColorController::class, 'store'])->name('store');
        Route::patch('/{color}/update', [ColorController::class, 'update'])->name('update');
        Route::delete('/{color}/destroy', [ColorController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('feature')->name('feature.')->group(function () {
        Route::post('/store', [FeatureController::class, 'store'])->name('store');
        Route::patch('/{feature}/update', [FeatureController::class, 'update'])->name('update');
        Route::delete('/{feature}/destroy', [FeatureController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('product')->name('product.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
    });

    Route::prefix('/user')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::delete('/destroy', BulkUserDeleteController::class)->name('destroy');
        });
        Route::patch('/store', [UserController::class, 'store'])->name('store');
        Route::prefix('{user}')->group(function () {
            Route::get('/', [UserController::class, 'show'])->name('detail');
            Route::patch('/update', [UserController::class, 'update'])->name('update');
            Route::delete('/destroy', [UserController::class, 'destroy'])->name('destroy');
            Route::prefix('api-token')->name('api-token.')->group(function () {
                Route::get('/', [ApiTokenController::class, 'index'])->name('index');
                Route::post('/store', [ApiTokenController::class, 'store'])->name('store');
                Route::prefix('{personalAccessToken}')->group(function () {
                    Route::get('/', [ApiTokenController::class, 'show'])->name('show');
                    Route::patch('/update', [ApiTokenController::class, 'update'])->name('update');
                    Route::delete('/destroy', [ApiTokenController::class, 'destroy'])->name('destroy');
                });
            });
            Route::prefix('seller')->name('seller.')->group(function () {
                Route::post('/add', [UserSellerController::class, 'addUserSeller'])->name('store');
                Route::delete('/remove', [UserSellerController::class, 'removeUserSeller'])->name('destroy');
            });
            Route::prefix('role')->name('role.')->group(function () {
                Route::patch('/{role}/update', [RoleController::class, 'update'])->name('update');
            });
        });
    });

    Route::prefix('role')->name('role.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('detail');
        Route::post('/store', [RoleController::class, 'store'])->name('store');
        Route::patch('/{role}/update', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}/destroy', [RoleController::class, 'delete'])->name('destroy');
    });

    Route::prefix('firebase')->name('firebase.')->group(function () {
        Route::prefix('device')->name('device.')->group(function () {
            Route::prefix('messaging')->name('messaging.')->group(function () {
                Route::post('/send', [FirebaseMessageController::class, 'sendMessageToDevice'])->name('send');
            });
            Route::post('/register', [FirebaseDeviceController::class, 'registerFirebaseDevice'])->name('register');
            Route::post('/store', [FirebaseDeviceController::class, 'storeFirebaseDevice'])->name('store');
            Route::prefix('{firebaseDevice}')->group(function () {
                Route::patch('/update', [FirebaseDeviceController::class, 'updateFirebaseDevice'])->name('update');
                Route::delete('/destroy', [FirebaseDeviceController::class, 'deleteFirebaseDevice'])->name('destroy');
            });
        });
        Route::prefix('topic')->name('topic.')->group(function () {
            Route::prefix('messaging')->name('messaging.')->group(function () {
                Route::post('/send', [FirebaseMessageController::class, 'sendMessageToTopic'])->name('send');
            });
            Route::prefix('{firebaseTopic}')->group(function () {
                Route::patch('/update', [FirebaseTopicController::class, 'updateFirebaseTopic'])->name('update');
                Route::delete('/destroy', [FirebaseTopicController::class, 'deleteFirebaseTopic'])->name('destroy');
            });
        });
    });

    Route::prefix('locale')->name('locale.')->group(function () {
        Route::prefix('country')->name('country.')->group(function () {
            Route::post('/store', [CountryController::class, 'store'])->name('store');
            Route::prefix('bulk')->name('bulk.')->group(function () {
                Route::post('/store', [BulkCountryController::class, 'store'])->name('store');
                Route::delete('/destroy', [BulkCountryController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('{country}')->group(function () {
                Route::get('/', [CountryController::class, 'show'])->name('show');
                Route::patch('/update', [CountryController::class, 'update'])->name('update');
                Route::delete('/destroy', [CountryController::class, 'destroy'])->name('destroy');
            });
        });
        Route::prefix('currency')->name('currency.')->group(function () {
            Route::post('/store', [CurrencyController::class, 'store'])->name('store');

            Route::prefix('bulk')->name('bulk.')->group(function () {
                Route::post('/store', [BulkCurrencyController::class, 'store'])->name('store');
                Route::delete('/destroy', [BulkCurrencyController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('{currency}')->group(function () {
                Route::get('/', [CurrencyController::class, 'show'])->name('show');
                Route::patch('/update', [CurrencyController::class, 'update'])->name('update');
                Route::delete('/destroy', [CurrencyController::class, 'destroy'])->name('destroy');
            });
        });
        Route::prefix('region')->name('region.')->group(function () {
            Route::post('/store', [RegionController::class, 'store'])->name('store');

            Route::prefix('bulk')->name('bulk.')->group(function () {
                Route::post('/store', [BulkRegionController::class, 'store'])->name('store');
                Route::delete('/destroy', [BulkRegionController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('{region}')->group(function () {
                Route::get('/', [RegionController::class, 'show'])->name('show');
                Route::patch('/update', [RegionController::class, 'update'])->name('update');
                Route::delete('/destroy', [RegionController::class, 'destroy'])->name('destroy');
            });
        });
    });
    Route::prefix('block')->name('block.')->group(function () {
        Route::get('/', [BlockController::class, 'index'])->name('index');
        // Route::post('/store', [PageBlockController::class, 'store'])->name('store');
        Route::prefix('{block}')->group(function () {
            Route::get('/', [BlockController::class, 'show'])->name('show');
            Route::get('/sidebar', [BlockSidebarController::class, 'index'])->name('sidebar');

            // Route::patch('/update', [PageBlockController::class, 'update'])->name('update');
            // Route::delete('/destroy', [PageBlockController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('pagination')->name('pagination.')->group(function () {
        Route::get('/type', PaginationTypeController::class)->name('type');
        Route::get('/scroll/type', PaginationScrollTypeController::class)->name('scroll.type');
    });
    Route::prefix('page')->name('page.')->group(function () {
        Route::get('/', [PageController::class, 'index'])->name('index');
        Route::get('/show', [PageViewController::class, 'index'])->name('view.index');
        Route::post('/store', [PageController::class, 'store'])->name('store');
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::delete('/destroy', PageBulkDeleteController::class)->name('destroy');
        });
        Route::prefix('{page}')->group(function () {
            Route::get('/', [PageController::class, 'show'])->name('show');
            Route::patch('/update', [PageController::class, 'update'])->name('update');
            Route::delete('/destroy', [PageController::class, 'delete'])->name('destroy');
            Route::prefix('role')->name('role.')->group(function () {
                Route::get('/', [PageRoleController::class, 'index'])->name('index');
                Route::prefix('{role}')->group(function () {
                    Route::post('/store', [PageRoleController::class, 'store'])->name('store');
                    Route::delete('/destroy', [PageRoleController::class, 'destroy'])->name('destroy');
                });
            });
            Route::prefix('sidebar')->name('sidebar.')->group(function () {
                Route::get('/', [PageSidebarController::class, 'index'])->name('index');
                Route::prefix('{sidebar}')->group(function () {
                    Route::post('/store', [PageSidebarController::class, 'store'])->name('store');
                    Route::delete('/destroy', [PageSidebarController::class, 'destroy'])->name('destroy');
                });
                Route::prefix('reorder')->name('reorder.')->group(function () {
                    Route::post('/', PageSidebarReorderController::class)->name('reorder');
                });
            });
            Route::prefix('block')->name('block.')->group(function () {
                Route::get('/', [PageBlockController::class, 'index'])->name('index');
                Route::post('/batch/destroy', BatchDeletePageBlockController::class)->name('batch.delete');

                Route::prefix('rel')->name('rel.')->group(function () {
                    Route::prefix('{pageBlock}')->group(function () {
                        Route::get('/', [PageBlockController::class, 'show'])->name('show');
                        Route::patch('/update', [PageBlockController::class, 'update'])->name('update');
                        Route::delete('/destroy', [PageBlockController::class, 'destroy'])->name('destroy');
                        Route::prefix('reorder')->name('reorder.')->group(function () {
                            Route::post('/', PageBlockReorderController::class)->name('reorder');
                        });
                        Route::prefix('role')->name('role.')->group(function () {
                            Route::get('/', [PageBlockRoleController::class, 'index'])->name('index');
                            Route::prefix('{role}')->group(function () {
                                Route::post('/store', [PageBlockRoleController::class, 'store'])->name('store');
                                Route::delete('/destroy', [PageBlockRoleController::class, 'destroy'])->name('destroy');
                            });
                        });
                        Route::prefix('sidebar')->name('sidebar.')->group(function () {
                            Route::get('/', [PageBlockSidebarController::class, 'index'])->name('index');
                            Route::prefix('{sidebar}')->group(function () {
                                Route::post('/store', [PageBlockSidebarController::class, 'store'])->name('store');
                            });

                            Route::prefix('rel')->name('rel.')->group(function () {
                                Route::prefix('{pageBlockSidebar}')->group(function () {
                                    Route::delete('/destroy', [PageBlockSidebarController::class, 'destroy'])->name('destroy');
                                    Route::prefix('reorder')->name('reorder.')->group(function () {
                                        Route::post('/', PageBlockSidebarReorderController::class)->name('reorder');
                                    });
                                });
                            });
                        });
                    });
                });
                Route::prefix('{block}')->group(function () {
                    Route::post('/store', [PageBlockController::class, 'store'])->name('store');
                });
            });
        });
    });

    Route::prefix('site')->name('site.')->group(function () {
        Route::get('/', [SiteController::class, 'index'])->name('index');
        Route::post('/store', [SiteController::class, 'store'])->name('store');
        Route::prefix('{site}')->group(function () {
            Route::patch('/update', [SiteController::class, 'update'])->name('update');
            Route::delete('/destroy', [SiteController::class, 'destroy'])->name('destroy');

            Route::prefix('token')->name('token.')->group(function () {
                Route::post('/store', [SiteTokenController::class, 'store'])->name('store');
                Route::prefix('{personalAccessToken}')->group(function () {
                    Route::delete('/destroy', [SiteTokenController::class, 'destroy'])->name('destroy');
                });
            });

            Route::prefix('settings')->name('settings.')->group(function () {
                Route::patch('/update', [SiteSettingController::class, 'update'])->name('update');
            });
        });
    });
    Route::prefix('app-menu')->name('app_menu.')->group(function () {
        Route::post('/store', [AppMenuController::class, 'store'])->name('store');
        Route::prefix('{appMenu}')->group(function () {
            Route::patch('/update', [AppMenuController::class, 'update'])->name('update');
            Route::delete('/destroy', [AppMenuController::class, 'destroy'])->name('destroy');
            Route::prefix('item')->name('item.')->group(function () {
                Route::post('/store', [AppMenuItemController::class, 'store'])->name('store');
                Route::prefix('{appMenuItem}')->group(function () {
                    Route::patch('/update', [AppMenuItemController::class, 'update'])->name('update');
                    Route::delete('/destroy', [AppMenuItemController::class, 'destroy'])->name('destroy');
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
        Route::post('/store', [MenuController::class, 'store'])->name('store');
        Route::get('/item/type', MenuItemTypeController::class)->name('item.type');
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::delete('/destroy', MenuBulkDeleteController::class)->name('destroy');
        });
        Route::prefix('{menu}')->group(function () {
            Route::patch('/update', [MenuController::class, 'update'])->name('update');
            Route::delete('/destroy', [MenuController::class, 'destroy'])->name('destroy');
            Route::prefix('role')->name('role.')->group(function () {
                Route::get('/', [MenuRoleController::class, 'index'])->name('index');
                Route::prefix('{role}')->group(function () {
                    Route::post('/store', [MenuRoleController::class, 'store'])->name('store');
                    Route::delete('/destroy', [MenuRoleController::class, 'destroy'])->name('destroy');
                });
            });
            Route::prefix('item')->name('item.')->group(function () {
                Route::get('/', [MenuItemController::class, 'index'])->name('index');
                Route::post('/store', [MenuItemController::class, 'store'])->name('store');
                Route::prefix('{menuItem}')->group(function () {
                    Route::get('/', [MenuItemController::class, 'show'])->name('show');
                    Route::patch('/update', [MenuItemController::class, 'update'])->name('update');
                    Route::delete('/destroy', [MenuItemController::class, 'destroy'])->name('destroy');
                    Route::prefix('reorder')->name('reorder.')->group(function () {
                        Route::post('/', MenuItemReorderController::class)->name('update');
                    });
                    Route::prefix('role')->name('role.')->group(function () {
                        Route::get('/', [MenuItemRoleController::class, 'index'])->name('index');
                        Route::prefix('{role}')->group(function () {
                            Route::post('/store', [MenuItemRoleController::class, 'store'])->name('store');
                            Route::delete('/destroy', [MenuItemRoleController::class, 'destroy'])->name('destroy');
                        });
                    });
                    Route::prefix('menu')->name('menu.')->group(function () {
                        Route::get('/', [MenuItemMenuController::class, 'index'])->name('index');
                        Route::prefix('{menuChild}')->group(function () {
                            Route::post('/store', [MenuItemMenuController::class, 'store'])->name('store');
                        });
                        Route::prefix('rel')->name('rel')->group(function () {
                            Route::prefix('{menuItemMenu}')->group(function () {
                                Route::delete('/destroy', [MenuItemMenuController::class, 'destroy'])->name('destroy');
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
        Route::post('/store', [SidebarController::class, 'store'])->name('store');
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::delete('/destroy', SidebarBulkDeleteController::class)->name('destroy');
        });
        Route::prefix('{sidebar}')->group(function () {
            Route::get('/', [SidebarController::class, 'show'])->name('show');
            Route::patch('/update', [SidebarController::class, 'update'])->name('update');
            Route::delete('/destroy', [SidebarController::class, 'destroy'])->name('destroy');
            Route::prefix('role')->name('role.')->group(function () {
                Route::get('/', [SidebarRoleController::class, 'index'])->name('index');
                Route::prefix('{role}')->group(function () {
                    Route::post('/store', [SidebarRoleController::class, 'store'])->name('store');
                    Route::delete('/destroy', [SidebarRoleController::class, 'destroy'])->name('destroy');
                });
            });
            Route::prefix('widget')->name('widget.')->group(function () {
                Route::get('/', [SidebarWidgetController::class, 'index'])->name('index');
                Route::prefix('rel')->name('rel')->group(function () {
                    Route::prefix('{sidebarWidget}')->group(function () {
                        Route::get('/', [SidebarWidgetController::class, 'show'])->name('show');
                        Route::patch('/update', [SidebarWidgetController::class, 'update'])->name('update');
                        Route::delete('/destroy', [SidebarWidgetController::class, 'destroy'])->name('destroy');
                        Route::prefix('reorder')->name('reorder.')->group(function () {
                            Route::post('/', SidebarWidgetReorderController::class)->name('reorder');
                        });
                        Route::prefix('role')->name('role.')->group(function () {
                            Route::get('/', [SidebarWidgetRoleController::class, 'index'])->name('index');
                            Route::prefix('{role}')->group(function () {
                                Route::post('/store', [SidebarWidgetRoleController::class, 'store'])->name('store');
                                Route::delete('/destroy', [SidebarWidgetRoleController::class, 'destroy'])->name('destroy');
                            });
                        });
                    });
                });
                Route::prefix('{widget}')->group(function () {
                    Route::post('/store', [SidebarWidgetController::class, 'store'])->name('store');
                });
            });
        });
    });
    Route::prefix('widget')->name('widget.')->group(function () {
        Route::get('/', [WidgetController::class, 'index'])->name('index');
        Route::post('/store', [WidgetController::class, 'store'])->name('store');
        Route::prefix('bulk')->name('bulk.')->group(function () {
            Route::delete('/destroy', WidgetBulkDeleteController::class)->name('destroy');
        });
        Route::prefix('{widget}')->group(function () {
            Route::get('/', [WidgetController::class, 'show'])->name('show');
            Route::patch('/update', [WidgetController::class, 'update'])->name('update');
            Route::delete('/destroy', [WidgetController::class, 'destroy'])->name('destroy');
            Route::prefix('role')->name('role.')->group(function () {
                Route::get('/', [WidgetRoleController::class, 'index'])->name('index');
                Route::prefix('{role}')->group(function () {
                    Route::post('/store', [WidgetRoleController::class, 'store'])->name('store');
                    Route::delete('/destroy', [WidgetRoleController::class, 'destroy'])->name('destroy');
                });
            });
        });
    });
});
