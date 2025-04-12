<?php
namespace App\Enums;

enum ViewType: string
{
    case Page = 'page';
    case AdminPage = 'admin_page';
    case AdminTabPage = 'admin_tab_page';
}
