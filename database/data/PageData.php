<?php

use App\Enums\Auth\ApiAbility;
use App\Enums\Block\BlockType;
use App\Enums\Product\ProductFetchProperty;
use App\Enums\Product\ProductCategory;
use App\Enums\ViewType;
use App\Enums\Widget\Widget;

return [
    [
        'site_id' => 1,
        'permalink' => '/',
        'name' => 'home',
        'title' => 'Home',
        'content' => 'Welcome to our home page',
        'view' => ViewType::Page,
        'blocks' => [
            [
                'type' => BlockType::HERO,
                'order' => 0,
                'title' => 'Welcome to our website',
                'subtitle' => 'We are a team of professionals',
                'background_image' => 'https://via.placeholder.com/1920x1080',
            ],
            [
                'type' => BlockType::FEATURED,
                'order' => 1,
                'title' => 'Welcome to our website',
                'subtitle' => 'We are a team of professionals',
                'background_image' => 'https://via.placeholder.com/1920x1080',
            ],
            [
                'type' => BlockType::PRODUCTS_GRID,
                'order' => 2,
                'title' => 'Welcome to our website',
                'subtitle' => 'We are a team of professionals',
                'background_image' => 'https://via.placeholder.com/1920x1080',
                'pagination' => true,
                'pagination_type' => 'page',
                'pagination_scroll_type' => 'block',
                'has_sidebar' => true,
                'properties' => [
                    'init' => [
                        ProductFetchProperty::TYPE->value => [ProductCategory::EVENT->value],
                    ],
                ],
            ],
        ],
        'roles' => [
            ApiAbility::USER->value,
            ApiAbility::SITE->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/about',
        'name' => 'about',
        'title' => 'About',
        'content' => 'Welcome to our about page',
        'view' => ViewType::Page,
        'blocks' => [
            [
                'type' => BlockType::HERO,
                'order' => 0,
                'title' => 'Welcome to our website',
                'subtitle' => 'We are a team of professionals',
                'background_image' => 'https://via.placeholder.com/1920x1080',
            ],
            [
                'type' => BlockType::ICON_GRID,
                'order' => 1,
                'title' => 'Welcome to our website',
                'subtitle' => 'We are a team of professionals',
                'background_image' => 'https://via.placeholder.com/1920x1080',
            ],
            [
                'type' => BlockType::PRODUCTS_GRID,
                'order' => 2,
                'title' => 'Welcome to our website',
                'subtitle' => 'We are a team of professionals',
                'background_image' => 'https://via.placeholder.com/1920x1080',
                'pagination' => true,
                'pagination_type' => 'page',
                'pagination_scroll_type' => 'block',
                'has_sidebar' => true,
            ],
        ],
        'roles' => [
            ApiAbility::USER->value,
            ApiAbility::SITE->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/contact',
        'name' => 'contact',
        'title' => 'Contact',
        'content' => 'Welcome to our contact page',
        'view' => ViewType::Page,
        'blocks' => [
            [
                'type' => BlockType::HERO,
                'order' => 0,
                'title' => 'Welcome to our website',
                'subtitle' => 'We are a team of professionals',
                'background_image' => 'https://via.placeholder.com/1920x1080',
            ],
            [
                'type' => BlockType::FEATURED,
                'order' => 1,
                'title' => 'Welcome to our website',
                'subtitle' => 'We are a team of professionals',
                'background_image' => 'https://via.placeholder.com/1920x1080',
            ],
        ],
        'roles' => [
            ApiAbility::USER->value,
            ApiAbility::SITE->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/login',
        'name' => 'login',
        'title' => 'Login',
        'content' => 'Welcome to our login page',
        'view' => ViewType::Page,
        'blocks' => [
            [
                'type' => BlockType::LOGIN,
                'order' => 0,
                'title' => 'Log into your account',
            ],
        ],
        'roles' => [
            ApiAbility::USER->value,
            ApiAbility::SITE->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/register',
        'name' => 'register',
        'title' => 'Register',
        'content' => 'Welcome to our register page',
        'view' => ViewType::Page,
        'blocks' => [
            [
                'type' => BlockType::REGISTER,
                'order' => 0,
                'title' => 'Register for an account',
            ],
        ],
        'roles' => [
            ApiAbility::USER->value,
            ApiAbility::SITE->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/account',
        'name' => 'account',
        'title' => 'My Account',
        'content' => 'Welcome to your account page',
        'view' => ViewType::AdminPage,
        'blocks' => [],
        'roles' => [
            ApiAbility::USER->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/account/dashboard',
        'name' => 'dashboard',
        'title' => 'Dashboard',
        'content' => 'Welcome to your dashboard page',
        'view' => ViewType::AdminPage,
        'blocks' => [],
        'roles' => [
            ApiAbility::USER->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/account/profile',
        'name' => 'profile',
        'title' => 'Profile',
        'content' => 'Welcome to your profile page',
        'view' => ViewType::AdminPage,
        'blocks' => [],
        'roles' => [
            ApiAbility::USER->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/account/settings',
        'name' => 'account_settings',
        'title' => 'Account Settings',
        'content' => 'Welcome to your account settings page',
        'view' => ViewType::AdminPage,
        'blocks' => [],
        'roles' => [
            ApiAbility::USER->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/admin',
        'name' => 'admin',
        'title' => 'Admin',
        'content' => 'Welcome to the admin page',
        'view' => ViewType::AdminTabPage,
        'blocks' => [
            [
                'type' => BlockType::MANAGE_PAGES,
                'order' => 0,
                'title' => 'Manage Pages',
                'nav_title' => 'Pages',
                'subtitle' => 'Manage your pages here',
                'default' => true,
            ],
            [
                'type' => BlockType::MANAGE_MENUS,
                'order' => 0,
                'title' => 'Manage Menus',
                'nav_title' => 'Menus',
                'subtitle' => 'Manage your menus here',
            ],
            [
                'type' => BlockType::MANAGE_SIDEBARS,
                'order' => 0,
                'title' => 'Manage Sidebars',
                'nav_title' => 'Sidebars',
                'subtitle' => 'Manage your sidebars here',
            ],
            [
                'type' => BlockType::MANAGE_WIDGETS,
                'order' => 0,
                'title' => 'Manage Widgets',
                'nav_title' => 'Widgets',
                'subtitle' => 'Manage your widgets here',
            ],
        ],
        'roles' => [
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/admin/dashboard',
        'name' => 'dashboard',
        'title' => 'Dashboard',
        'content' => 'Welcome to your dashboard page',
        'view' => ViewType::AdminPage,
        'blocks' => [],
        'roles' => [
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
            ApiAbility::USER->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/admin/product',
        'name' => 'admin_product',
        'title' => 'Product',
        'content' => 'Welcome to the product admin page',
        'view' => ViewType::AdminTabPage,
        'blocks' => [
            [
                'type' => BlockType::MANAGE_PRODUCTS,
                'order' => 0,
                'title' => 'Manage Products',
                'nav_title' => 'Products',
                'subtitle' => 'Manage your products here',
                'default' => true,
            ],
            [
                'type' => BlockType::MANAGE_BRANDS,
                'order' => 0,
                'title' => 'Manage Brands',
                'nav_title' => 'Brands',
                'subtitle' => 'Manage your brands here',
                'default' => false,
            ],
            [
                'type' => BlockType::MANAGE_COLORS,
                'order' => 0,
                'title' => 'Manage Colors',
                'nav_title' => 'Colors',
                'subtitle' => 'Manage your colors here',
                'default' => false,
            ],
            [
                'type' => BlockType::MANAGE_CATEGORIES,
                'order' => 0,
                'title' => 'Manage Categories',
                'nav_title' => 'Categories',
                'subtitle' => 'Manage your categories here',
                'default' => false,
            ],
            [
                'type' => BlockType::MANAGE_FEATURES,
                'order' => 0,
                'title' => 'Manage Features',
                'nav_title' => 'Features',
                'subtitle' => 'Manage your features here',
                'default' => false,
            ],
            [
                'type' => BlockType::MANAGE_PRODUCT_TYPES,
                'order' => 0,
                'title' => 'Manage Product Types',
                'nav_title' => 'Product Types',
                'subtitle' => 'Manage your product types here',
                'default' => false,
            ],
            [
                'type' => BlockType::MANAGE_PRODUCT_TYPES,
                'order' => 0,
                'title' => 'Manage Product Types',
                'nav_title' => 'Product Types',
                'subtitle' => 'Manage your product types here',
                'default' => false,
            ],
            [
                'type' => BlockType::MANAGE_REVIEWS,
                'order' => 0,
                'title' => 'Manage Reviews',
                'nav_title' => 'Reviews',
                'subtitle' => 'Manage your reviews here',
                'default' => false,
            ],
        ],
        'roles' => [
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
            ApiAbility::USER->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/admin/user',
        'name' => 'admin_users',
        'title' => 'User',
        'content' => 'Welcome to the user admin page',
        'view' => ViewType::AdminTabPage,
        'blocks' => [
            [
                'type' => BlockType::MANAGE_USERS,
                'order' => 0,
                'title' => 'Manage Users',
                'nav_title' => 'Users',
                'subtitle' => 'Manage your users here',
                'default' => true,
            ],
        ],
        'roles' => [
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
            ApiAbility::USER->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/admin/settings',
        'name' => 'admin_settings',
        'title' => 'Settings',
        'content' => 'Welcome to the settings admin page',
        'view' => ViewType::AdminPage,
        'blocks' => [
            [
                'type' => BlockType::MANAGE_SITE_SETTINGS,
                'order' => 0,
                'title' => 'Manage Products',
                'nav_title' => 'Products',
                'subtitle' => 'Manage your products here',
                'default' => true,
            ],
        ],
        'roles' => [
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/admin/finance/manage',
        'name' => 'admin_finance',
        'title' => 'Finance',
        'content' => 'Welcome to the finance admin page',
        'view' => ViewType::AdminTabPage,
        'blocks' => [
            [
                'type' => BlockType::MANAGE_DISCOUNTS,
                'order' => 0,
                'title' => 'Manage Discounts',
                'nav_title' => 'Discounts',
                'subtitle' => 'Manage your discounts here',
                'default' => true,
            ],
            [
                'type' => BlockType::MANAGE_PAYMENT_GATEWAYS,
                'order' => 0,
                'title' => 'Manage Payment Gateways',
                'nav_title' => 'Payment Gateways',
                'subtitle' => 'Manage your payment gateways here',
                'default' => false,
            ],
            [
                'type' => BlockType::MANAGE_TAX_RATES,
                'order' => 0,
                'title' => 'Manage Tax Rates',
                'nav_title' => 'Tax Rates',
                'subtitle' => 'Manage your tax rates here',
                'default' => false,
            ],
        ],
        'roles' => [
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/admin/profile/manage_address',
        'name' => 'manage_address',
        'title' => 'Manage Address',
        'content' => 'Welcome to the manage address admin page',
        'view' => ViewType::AdminPage,
        'blocks' => [
            [
                'type' => BlockType::MANAGE_ADDRESSES,
                'order' => 0,
                'title' => 'Manage Addresses',
                'nav_title' => 'Addresses',
                'subtitle' => 'Manage your addresses here',
                'default' => true,
            ],
        ],
        'roles' => [
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
            ApiAbility::USER->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/admin/shipping',
        'name' => 'admin_shipping',
        'title' => 'Shipping',
        'content' => 'Welcome to the shipping admin page',
        'view' => ViewType::AdminTabPage,
        'blocks' => [
            [
                'type' => BlockType::MANAGE_SHIPPING_METHODS,
                'order' => 0,
                'title' => 'Manage Shipping Methods',
                'nav_title' => 'Shipping Methods',
                'subtitle' => 'Manage your shipping methods here',
                'default' => true,
            ],
            [
                'type' => BlockType::MANAGE_SHIPPING_ZONES,
                'order' => 0,
                'title' => 'Manage Shipping Zones',
                'nav_title' => 'Shipping Zones',
                'subtitle' => 'Manage your shipping zones here',
                'default' => false,
            ],
        ],
        'roles' => [
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
            ApiAbility::USER->value,
        ]
    ],
];
