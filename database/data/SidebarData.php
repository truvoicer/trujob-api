<?php

use App\Enums\Widget\Widget;

return [
    [
        'site' => 'tru-job',
        "name" => "left-sidebar",
        "title" => "Left Sidebar",
        "icon" => "fa-bar",
        "roles" => [],
        "widgets" => [
            [
                "name" => Widget::SEARCH_FILTER->value,
                "title" => "Search Filter",
                "icon" => "fa-bar",
                "has_container" => false,
                "roles" => [
                    1,
                    2
                ]
            ],
            [
                "name" => Widget::PROXIMITY_FILTER->value,
                "title" => "Category Filter",
                "icon" => "fa-bar",
                "has_container" => false,
                "roles" => []
            ],
            [
                "name" => Widget::CATEGORY_FILTER->value,
                "title" => "Proximity Filter",
                "icon" => "fa-bar",
                "has_container" => false,
                "roles" => []
            ],

        ]
    ]
];
