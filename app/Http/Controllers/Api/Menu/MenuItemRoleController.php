<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Menu;
use App\Models\Role;
use App\Models\MenuItem;
use App\Services\Admin\Menu\MenuService;
use Illuminate\Http\Request;

class MenuItemRoleController extends Controller
{

    public function __construct(
        private MenuService $menuService
    ) {}

    public function index(
        Menu $menu,
        MenuItem $menuItem,
        Request $request
    ) {
        $this->menuService->setUser($request->user()->user);
        $this->menuService->setSite($request->user()->site);

        return RoleResource::collection(
            $this->menuService->getMenuRepository()
                ->getRoles($menuItem)

        );
    }
    public function store(
        Menu $menu,
        MenuItem $menuItem,
        Role $role,
        Request $request
    ) {
        $this->menuService->setUser($request->user()->user);
        $this->menuService->setSite($request->user()->site);
        
        $this->menuService->assignRoles(
            $menuItem->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role assigned to menu item.",
        ]);
    }
    public function destroy(
        Menu $menu,
        MenuItem $menuItem,
        Role $role,
        Request $request
    ) {
        $this->menuService->setUser($request->user()->user);
        $this->menuService->setSite($request->user()->site);

        $this->menuService->getMenuRepository()->detachRoles(
            $menuItem->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role removed from menu item.",
        ]);
    }
}
