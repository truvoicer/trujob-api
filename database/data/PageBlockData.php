<?php

use App\Enums\BlockType;

return [
    [
        'type' => BlockType::HERO,
        'properties' => [
            'title' => 'Welcome to our website',
            'subtitle' => 'We are a team of professionals',
            'background_image' => 'https://via.placeholder.com/1920x1080',
        ],
        'order' => 0,
    ],
    [
        'type' => BlockType::FEATURED,
        'properties' => [
            'title' => 'Our Services',
            'subtitle' => 'What we offer',
            'background_image' => 'https://via.placeholder.com/1920x1080',
        ],
        'order' => 1,
    ],
    [
        'type' => BlockType::ICON_GRID,
        'properties' => [
            'title' => 'Why choose us?',
            'subtitle' => 'We are the best',
            'background_image' => 'https://via.placeholder.com/1920x1080',
        ],
        'order' => 2,
    ],
    [
        'type' => BlockType::LISTINGS_GRID,
        'properties' => [
            'title' => 'Our Listings',
            'subtitle' => 'Check out our listings',
            'background_image' => 'https://via.placeholder.com/1920x1080',
            'item_container_class' => 'grid grid-cols-3 gap-4',
        ],
        'order' => 3,
    ],
];
