<?php

use App\Enums\BlockType;
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
