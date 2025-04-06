<?php

use App\Enums\Widget\Widget;

return [
    [
        'site_id' => 1,
        'name' => Widget::SEARCH_FILTER,
        'title' => 'Search Filter',
    ],
    [
        'site_id' => 1,
        'name' => Widget::PROXIMITY_FILTER,
        'title' => 'Proximity Filter',
        "roles" => [
            1,
            2
        ],
    ],
    [
        'site_id' => 1,
        'name' => Widget::CATEGORY_FILTER,
        'title' => 'Categoryss Filter',
        "roles" => [
            1,
            2
        ],
    ],
];
