<?php

namespace App\Helpers;

use App\Enums\MenuItemType;
use App\Models\MenuItem;
use App\Models\Page;

class MenuHelpers
{
    public static function getMenuItemUrl(MenuItemType $menuItemType, string|null $value, ?Page $page = null): string|null
    {
        switch ($menuItemType) {
            case MenuItemType::PAGE:
            case MenuItemType::LOGIN:
            case MenuItemType::LOGOUT:
            case MenuItemType::REGISTER:
                return $page?->permalink;
            case MenuItemType::URL:
                return $value;
            default:
                return null;
        }
    }
}
