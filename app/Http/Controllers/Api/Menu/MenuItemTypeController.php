<?php

namespace App\Http\Controllers\Api\Menu;

use App\Enums\MenuItemType;
use App\Http\Controllers\Controller;
use App\Services\Admin\Menu\MenuService;

class MenuItemTypeController extends Controller
{

    public function __construct(
        private MenuService $menuService
    )
    {}

    public function index()
    {
        return response()->json([
            'message' => 'Menu item types fetched successfully',
            'data' => MenuItemType::cases(),
        ]);
    }
}
