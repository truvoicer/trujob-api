<?php

namespace App\Http\Controllers\Api\Menu;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\CreateMenuItemMenuRequest;
use App\Http\Requests\Menu\CreateMenuItemRequest;
use App\Http\Requests\Menu\CreateMenuRequest;
use App\Http\Requests\Menu\EditMenuItemRequest;
use App\Http\Resources\Menu\MenuItemMenuResource;
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
            $request->get('sort', 'order')
        );
        $this->menuRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->menuRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->menuRepository->setPage(
            $request->get('page', 1)
        );
        $this->menuRepository->setWith([
            'menu',
            'menuItem',
        ]);

        return MenuItemMenuResource::collection(
            $this->menuRepository->findMany()
        );
    }

    public function store(Menu $menu, MenuItem $menuItem, Menu $menuChild, CreateMenuItemMenuRequest $request) {
        $this->menuService->setUser(request()->user()->user);
        $this->menuService->setSite(request()->user()->site);
        
        $this->menuService->addMenuToMenuItem($menuItem, [$menuChild->id]);
        
        return response()->json([
            'message' => 'Menu item menu created',
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
