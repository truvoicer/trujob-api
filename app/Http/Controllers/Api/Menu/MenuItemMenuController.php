<?php

namespace App\Http\Controllers\Api\Menu;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\CreateMenuItemRequest;
use App\Http\Requests\Menu\CreateMenuRequest;
use App\Http\Requests\Menu\EditMenuItemRequest;
use App\Http\Resources\Menu\MenuResource;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemMenu;
use App\Repositories\MenuRepository;
use App\Services\Admin\Menu\MenuService;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class MenuItemMenuController extends Controller
{

    public function __construct(
        private MenuService $menuService,
        private MenuRepository $menuRepository
    )
    {}

    public function index(Menu $menu, MenuItem $menuItem, Request $request) {
        $this->menuService->setUser(request()->user()->user);
        $this->menuService->setSite(request()->user()->site);

        $this->menuRepository->setQuery(
            $menuItem->menuItemMenus()
        );
        $this->menuRepository->setPagination(true);
        $this->menuRepository->setSortField(
            $request->get('sort', 'created_at')
        );
        $this->menuRepository->setOrderDir(
            $request->get('order', 'desc')
        );
        $this->menuRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->menuRepository->setPage(
            $request->get('page', 1)
        );
        $this->menuRepository->setWith([
            'menus' => function ($query) {
                $query->orderBy('order', 'asc');
            },
        ]);

        return MenuResource::collection(
            $this->menuRepository->findMany()
        );
    }

    public function create(Menu $menu, MenuItem $menuItem, Menu $menuChild, CreateMenuItemRequest $request) {
        $this->menuService->setUser(request()->user()->user);
        $this->menuService->setSite(request()->user()->site);
        
        $create = $this->menuService->createMenuItem(
            $menu,
            [
                ...$request->validated(),
                'menus' => $request->validated('menus'),
            ]
        );
        if (!$create) {
            return response()->json([
                'message' => 'Error creating app menu item',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'MenuItem created',
        ]);
    }

    
    public function destroy(Menu $menu, MenuItem $menuItem, MenuItemMenu $menuItemMenu) {
        $this->menuService->setUser(request()->user()->user);
        $this->menuService->setSite(request()->user()->site);
        $this->menuService->deleteMenuItemMenu($menuItemMenu);
        return response()->json([
            'message' => 'Menu item menu deleted',
        ]);
    }
}
