<?php

use App\Enums\Auth\ApiAbility;
use App\Enums\Block\BlockType;
use App\Enums\Listing\ListingFetchProperty;
use App\Enums\Listing\ListingType;
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
                'type' => BlockType::LISTINGS_GRID,
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
                        ListingFetchProperty::TYPE->value => [ListingType::EVENT->value],
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
                'type' => BlockType::LISTINGS_GRID,
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
            [
                'type' => BlockType::MANAGE_USERS,
                'order' => 0,
                'title' => 'Manage Users',
                'nav_title' => 'Users',
                'subtitle' => 'Manage your users here',
            ],
        ],
        'roles' => [
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
        ]
    ],
    [
        'site_id' => 1,
        'permalink' => '/admin/listing',
        'name' => 'admin_listing',
        'title' => 'Listing',
        'content' => 'Welcome to the listing admin page',
        'view' => ViewType::AdminTabPage,
        'blocks' => [
            [
                'type' => BlockType::MANAGE_LISTINGS,
                'order' => 0,
                'title' => 'Manage Listings',
                'nav_title' => 'Listings',
                'subtitle' => 'Manage your listings here',
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
                'type' => BlockType::MANAGE_LISTING_TYPES,
                'order' => 0,
                'title' => 'Manage Listing Types',
                'nav_title' => 'Listing Types',
                'subtitle' => 'Manage your listing types here',
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
];
