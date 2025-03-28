<?php
namespace App\Enums\Block;

enum BlockType: string
{
    case HERO = 'hero-block';
    case FEATURED = 'featured-block';
    case ICON_GRID = 'icon-grid-block';
    case LISTINGS_GRID = 'listings-grid-block';
    case LOGIN = 'login-block';
    case REGISTER = 'register-block';
}
