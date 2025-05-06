<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuItemMenuReorderRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemMenu;
use App\Services\Admin\Menu\MenuService;

class MenuItemMenuReorderController extends Controller
{

    public function __construct(
        private MenuService $menuService
    )
    {}

    public function __invoke(Menu $menu, MenuItem $menuItem, MenuItemMenu $menuItemMenu, MenuItemMenuReorderRequest $request) {
        $this->menuService->setUser($request->user()->user);
        $this->menuService->setSite($request->user()->site);
        $this->menuService->moveMenuItemMenu(
            $menuItem,
            $menuItemMenu,
            $request->validated('direction')
        );
        return response()->json([
            'message' => "Menu item menu moved {$request->validated('direction')}",
        ]);
    }
}
