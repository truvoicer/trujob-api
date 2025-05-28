<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Menu;
use App\Models\Role;
use App\Services\Admin\Menu\MenuService;
use Illuminate\Http\Request;

class MenuRoleController extends Controller
{

    public function __construct(
        private MenuService $menuService
    ) {}

    public function index(
        Menu $menu,
        Request $request
    ) {
        $this->menuService->setUser($request->user()->user);
        $this->menuService->setSite($request->user()->site);

        return RoleResource::collection(
            $this->menuService->getMenuRepository()
                ->getRoles($menu)

        );
    }
    public function store(
        Menu $menu,
        Role $role,
        Request $request
    ) {
        $this->menuService->setUser($request->user()->user);
        $this->menuService->setSite($request->user()->site);
        
        $this->menuService->assignRoles(
            $menu->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role assigned to menu.",
        ]);
    }
    public function destroy(
        Menu $menu,
        Role $role,
        Request $request
    ) {
        $this->menuService->setUser($request->user()->user);
        $this->menuService->setSite($request->user()->site);

        $this->menuService->getMenuRepository()->detachRoles(
            $menu->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role removed from menu.",
        ]);
    }
}
