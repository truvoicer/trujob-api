<?php
namespace App\Enums;

enum MenuItemType: string
{
    case PAGE = 'page';
    case URL = 'url';
    case MENU = 'menu';
}
