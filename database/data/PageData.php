<?php

use App\Enums\Auth\ApiAbility;
use App\Enums\Block\BlockType;
use App\Enums\Listing\ListingFetchProperty;
use App\Enums\Listing\ListingBlockSidebarWidget;
use App\Enums\Listing\ListingType;
use App\Enums\ViewType;

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
                'sidebar_widgets' => [
                    [
                        'type' => ListingBlockSidebarWidget::SEARCH_FILTER->value,
                        'has_container' => false,
                        'title' => 'Search',
                    ],
                    [
                        'type' => ListingBlockSidebarWidget::PROXIMITY_FILTER->value,
                        'has_container' => false,
                        'title' => 'Set Proximity',
                    ],
                    [
                        'type' => ListingBlockSidebarWidget::CATEGORY_FILTER->value,
                        'has_container' => false,
                        'title' => 'Choose Category/s',
                    ],
                ],
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
                'sidebar_widgets' => [
                    [
                        'type' => ListingBlockSidebarWidget::SEARCH_FILTER->value,
                        'has_container' => false,
                        'title' => 'Search',
                    ],
                    [
                        'type' => ListingBlockSidebarWidget::PROXIMITY_FILTER->value,
                        'has_container' => false,
                        'title' => 'Set Proximity',
                    ],
                    [
                        'type' => ListingBlockSidebarWidget::CATEGORY_FILTER->value,
                        'has_container' => false,
                        'title' => 'Choose Category/s',
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
        'permalink' => '/admin/manage-pages',
        'name' => 'admin_manage_pages',
        'title' => 'Manage Pages',
        'content' => 'Welcome to the admin manage pages page',
        'view' => ViewType::AdminPage,
        'blocks' => [
            [
                'type' => BlockType::MANAGE_PAGES,
                'order' => 0,
                'title' => 'Manage Pages',
                'subtitle' => 'Manage your pages here',
            ],
        ],
        'roles' => [
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
        ]
    ],
];
