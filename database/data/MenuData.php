<?php

use App\Enums\Auth\ApiAbility;
use App\Enums\MenuItemType;

return [
    [
        'site' => 'tru-job',
        'name' => 'header-about-menu',
        'menu_items' => [
            [
                'page_name' => 'about',
                'label' => 'About Us',
                'url' => '/about',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
            ],
            [
                'page_name' => 'contact',
                'label' => 'Contact',
                'url' => '/contact',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 1,
            ],
        ],
    ],
    [
        'site' => 'tru-job',
        'name' => 'header-account-menu',
        'menu_items' => [
            [
                'page_name' => 'dashboard',
                'label' => 'Dashboard',
                'url' => '/dashboard',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
            ],
            [
                'page_name' => 'profile',
                'label' => 'Profile',
                'url' => '/profile',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 1,
            ],
            [
                'label' => 'Logout',
                'url' => '/logout',
                'active' => true,
                'type' => MenuItemType::LOGOUT->value,
                'order' => 1,
            ],
        ],
        'roles' => [
            ApiAbility::USER->value,
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
        ],
    ],
    [
        'site' => 'tru-job',
        'name' => 'header-menu',
        'menu_items' => [
            [
                'page_name' => 'home',
                'label' => 'Home',
                'url' => '/',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
            ],

            [
                'label' => 'About',
                'url' => '/about',
                'active' => true,
                'type' => MenuItemType::URL->value,
                'order' => 1,
                'menus' => [
                    'name' => 'header-about-menu',
                ]
            ],
            [
                'page_name' => 'login',
                'label' => 'Login',
                'url' => '/login',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 2,
            ],
            [
                'page_name' => 'register',
                'label' => 'Register',
                'url' => '/register',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 3,
            ],
            [
                'label' => 'My Account',
                'url' => '/account',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 4,
                'roles' => [
                    ApiAbility::USER->value,
                    ApiAbility::SUPERUSER->value,
                    ApiAbility::ADMIN->value,
                ],
                'menus' => [
                    'name' => 'header-account-menu',
                ]
            ],
        ],
    ],
    [
        'site' => 'tru-job',
        'name' => 'admin-profile-menu',
        'menu_items' => [
            [
                'page_name' => 'manage_address',
                'label' => 'Manage Address',
                'url' => '/admin/profile/manage-address',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
            ],
        ],
        'roles' => [
            ApiAbility::USER->value,
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
        ],
    ],
    [
        'site' => 'tru-job',
        'name' => 'admin-header-menu',
        'menu_items' => [
            [
                'page_name' => 'admin',
                'label' => 'Admin',
                'url' => '/admin',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
            ],
            [
                'page_name' => 'admin_finance',
                'label' => 'Finance',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
            ],
            [
                'page_name' => 'admin_product',
                'label' => 'Product',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
            ],
            [
                'label' => 'Profile',
                'url' => '/admin/profile',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
                'roles' => [
                    ApiAbility::USER->value,
                    ApiAbility::SUPERUSER->value,
                    ApiAbility::ADMIN->value,
                ],
                'menus' => [
                    'name' => 'admin-profile-menu',
                ]
            ],
            [
                'page_name' => 'admin_settings',
                'label' => 'Settings',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
            ],
            [
                'page_name' => 'admin_shipping',
                'label' => 'Shipping',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
            ],
            [
                'page_name' => 'admin_users',
                'label' => 'Users',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
            ],
        ],
        'roles' => [
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
        ],
    ],
    [
        'site' => 'tru-job',
        'name' => 'admin-header-user-dropdown-menu',
        'menu_items' => [
            [

                'page_name' => 'admin_profile',
                'label' => 'Profile',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
                'roles' => [
                    ApiAbility::SUPERUSER->value,
                    ApiAbility::ADMIN->value,
                    ApiAbility::USER->value,
                ],
            ],
            [

                'page_name' => 'admin_account_settings',
                'label' => 'Account Settings',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
                'roles' => [
                    ApiAbility::SUPERUSER->value,
                    ApiAbility::ADMIN->value,
                    ApiAbility::USER->value,
                ],
            ],
        ],
    ],
];
