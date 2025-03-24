<?php
namespace App\Enums;

enum MenuItemType: string
{
    case PAGE = 'page';
    case URL = 'url';
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case REGISTER = 'register';
}
