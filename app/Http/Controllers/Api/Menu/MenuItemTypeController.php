<?php

namespace App\Http\Controllers\Api\Menu;

use App\Enums\MenuItemType;
use App\Http\Controllers\Controller;

class MenuItemTypeController extends Controller
{

    public function index()
    {
        return response()->json([
            'message' => 'Menu item types fetched successfully',
            'data' => MenuItemType::cases(),
        ]);
    }
}
