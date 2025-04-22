<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuItemReorderRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\Admin\Menu\MenuService;

class MenuItemReorderController extends Controller
{

    public function __construct(
        private MenuService $menuService
    )
    {}

    public function __invoke(Menu $menu, MenuItem $menuItem, MenuItemReorderRequest $request) {
        $this->menuService->setUser($request->user()->user);
        $this->menuService->setSite($request->user()->site);
        $this->menuService->moveMenuItem(
            $menu,
            $menuItem,
            $request->validated('direction')
        );
        return response()->json([
            'message' => "Menu item moved {$request->validated('direction')}",
        ]);
    }
}
