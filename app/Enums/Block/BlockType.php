<?php
namespace App\Enums\Block;

use App\Enums\Widget\Widget;

enum BlockType: string
{
    case HERO = 'hero-block';
    case FEATURED = 'featured-block';
    case ICON_GRID = 'icon-grid-block';
    case LISTINGS_GRID = 'listings-grid-block';
    case LOGIN = 'login-block';
    case REGISTER = 'register-block';
    case MANAGE_PAGES = 'manage-pages-block';
    case MANAGE_LISTINGS = 'manage-listings-block';
    case MANAGE_SIDEBARS = 'manage-sidebars-block';
    case MANAGE_WIDGETS = 'manage-widgets-block';
    case MANAGE_MENUS = 'manage-menus-block';
    case MANAGE_USERS = 'manage-users-block';

    public function isAdminBlock(): bool
    {
        return match ($this) {
            BlockType::MANAGE_PAGES,
            BlockType::MANAGE_LISTINGS,
            BlockType::MANAGE_SIDEBARS,
            BlockType::MANAGE_WIDGETS,
            BlockType::MANAGE_USERS,
            BlockType::MANAGE_MENUS => true,
            default => false,
        };
    }

    public function getSidebarWidgets(): array
    {
        return match ($this) {
            BlockType::LISTINGS_GRID => Widget::cases(),
            default => [],
        };
    }
}
