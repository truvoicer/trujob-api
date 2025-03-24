<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\CreateMenuItemRequest;
use App\Http\Requests\Menu\CreateMenuRequest;
use App\Http\Requests\Menu\EditMenuItemRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\Admin\Menu\MenuService;
use Symfony\Component\HttpFoundation\Response;

class MenuItemController extends Controller
{

    public function __construct(
        private MenuService $menuService
    )
    {}

    public function create(Menu $menu, CreateMenuItemRequest $request) {
        $this->menuService->setUser($request->user());
        $this->menuService->setMenu($menu);
        $create = $this->menuService->createMenuItem($menu, $request->validated());
        if (!$create) {
            return response()->json([
                'message' => 'Error creating app menu item',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'MenuItem created',
        ]);
    }

    public function update(MenuItem $menuItem, EditMenuItemRequest $request) {
        $this->menuService->setUser($request->user());
        $this->menuService->setMenuItem($menuItem);
        $update = $this->menuService->updateMenuItem($menuItem, $request->validated());
        if (!$update) {
            return response()->json([
                'message' => 'Error updating app menu item',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'MenuItem updated',
        ]);
    }
    public function addMenuToMenuItem(Menu $menu, MenuItem $menuItem, CreateMenuRequest $request) {
        $this->menuService->setUser($request->user());
        $this->menuService->setMenu($menu);
        $this->menuService->setMenuItem($menuItem);
        $update = $this->menuService->addMenuToMenuItem($menuItem, $request->validated());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating app menu item',
                [],
                $this->menuService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('MenuItem updated', [], $this->menuService->getResultsService()->getErrors());
    }

    public function removeMenuItemFromMenu(Menu $menu, MenuItem $menuItem) {
        $this->menuService->setMenu($menu);
        $this->menuService->setMenuItem($menuItem);
        if (!$this->menuService->removeMenuItem()) {
            return $this->sendErrorResponse(
                'Error removing app menu item',
                [],
                $this->menuService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('MenuItem removed', [], $this->menuService->getResultsService()->getErrors());
    }
    public function destroy(MenuItem $menuItem) {
        $this->menuService->setMenuItem($menuItem);
        if (!$this->menuService->deleteMenuItem()) {
            return response()->json([
                'message' => 'Error deleting app menu item',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'MenuItem deleted',
        ]);
    }
}
