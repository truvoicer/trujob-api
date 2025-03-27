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
                'title' => 'Home',
                'url' => '/',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 0,
            ],
            [
                'title' => 'About',
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
                                'title' => 'About Us',
                                'url' => '/about',
                                'active' => true,
                                'type' => MenuItemType::PAGE->value,
                                'order' => 0,
                            ],
                            [
                                'page' => 'contact',
                                'title' => 'Contact',
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
                'title' => 'Login',
                'url' => '/login',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 2,
            ],
            [
                'page' => 'register',
                'title' => 'Register',
                'url' => '/register',
                'active' => true,
                'type' => MenuItemType::PAGE->value,
                'order' => 3,
            ],
            [
                'title' => 'My Account',
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
                                'title' => 'Dashboard',
                                'url' => '/dashboard',
                                'active' => true,
                                'type' => MenuItemType::PAGE->value,
                                'order' => 0,
                            ],
                            [
                                'page' => 'profile',
                                'title' => 'Profile',
                                'url' => '/profile',
                                'active' => true,
                                'type' => MenuItemType::PAGE->value,
                                'order' => 1,
                            ],
                            [
                                'title' => 'Logout',
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
];
