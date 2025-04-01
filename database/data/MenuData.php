<?php

use App\Enums\Auth\ApiAbility;
use App\Enums\MenuItemType;

return [
    [
        'site' => 'tru-job',
        'name' => 'header-menu',
        'menu_items' => [
            [
                'page' => 'home',
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
                    [
                        'name' => 'header-about-menu',
                        'menu_items' => [
                            [
                                'page' => 'about',
                                'label' => 'About Us',
                                'url' => '/about',
                                'active' => true,
                                'type' => MenuItemType::PAGE->value,
                                'order' => 0,
                            ],
                            [
                                'page' => 'contact',
                                'label' => 'Contact',
                                'url' => '/contact',
                                'active' => true,
                                'type' => MenuItemType::PAGE->value,
                                'order' => 1,
                            ],
                        ],
                    ]
                ]
            ],
            [
                'page' => 'login',
                'label' => 'Login',
                'url' => '/login',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 2,
            ],
            [
                'page' => 'register',
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
                ],
                'menus' => [
                    [
                        'name' => 'header-account-menu',
                        'menu_items' => [
                            [
                                'page' => 'dashboard',
                                'label' => 'Dashboard',
                                'url' => '/dashboard',
                                'active' => true,
                                'type' => MenuItemType::PAGE->value,
                                'order' => 0,
                            ],
                            [
                                'page' => 'profile',
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
                        ],
                    ]
                ]
            ],
        ],
    ],
    [
        'site' => 'tru-job',
        'name' => 'admin-header-menu',
        'menu_items' => [
            [

                'page' => 'dashboard',
                'label' => 'Admin',
                'url' => '/dashboard',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
                'roles' => [
                    ApiAbility::SUPERUSER->value,
                    ApiAbility::ADMIN->value,
                ],
                'menus' => [
                    [
                        'name' => 'admin-header-admin-menu',
                        'menu_items' => [
                            [
                                'page' => 'admin_manage_pages',
                                'label' => 'Manage Pages',
                                'url' => '/admin/manage-pages',
                                'active' => true,
                                'type' => MenuItemType::PAGE->value,
                                'order' => 0,
                            ],
                        ],
                        'roles' => [
                            ApiAbility::SUPERUSER->value,
                            ApiAbility::ADMIN->value,
                        ],
                    ]
                ]
            ],
        ],
    ],
    [
        'site' => 'tru-job',
        'name' => 'admin-header-user-dropdown-menu',
        'menu_items' => [
            [

                'page' => 'account_settings',
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
