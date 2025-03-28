<?php
namespace App\Enums\Block;

enum AdminPageBlockType: string
{
    case LOGIN = 'login-block';
    case REGISTER = 'register-block';
    case MANAGE_PAGES = 'manage-pages-block';
    case MANAGE_LISTINGS = 'manage-listings-block';
}
