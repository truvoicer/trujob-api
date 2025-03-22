<?php

use App\Enums\BlockType;
use App\Enums\ListingsBlockSidebarWidget;
use App\Enums\ViewType;

return [
    [
        'slug' => 'home',
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
                        'type' => ListingsBlockSidebarWidget::SEARCH_FILTER->value,
                        'has_container' => false,
                        'title' => 'Search',
                    ],
                    [
                        'type' => ListingsBlockSidebarWidget::PROXIMITY_FILTER->value,
                        'has_container' => false,
                        'title' => 'Set Proximity',
                    ],
                    [
                        'type' => ListingsBlockSidebarWidget::CATEGORY_FILTER->value,
                        'has_container' => false,
                        'title' => 'Choose Category/s',
                    ],
                ],
            ],
        ]
    ],
    [
        'slug' => 'about',
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
                        'type' => ListingsBlockSidebarWidget::SEARCH_FILTER->value,
                        'has_container' => false,
                        'title' => 'Search',
                    ],
                    [
                        'type' => ListingsBlockSidebarWidget::PROXIMITY_FILTER->value,
                        'has_container' => false,
                        'title' => 'Set Proximity',
                    ],
                    [
                        'type' => ListingsBlockSidebarWidget::CATEGORY_FILTER->value,
                        'has_container' => false,
                        'title' => 'Choose Category/s',
                    ],
                ],
            ],

        ]
    ],
    [
        'slug' => 'contact',
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
        ]
    ],
];
