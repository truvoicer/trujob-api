<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\CreateMenuRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\Admin\Menu\MenuService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuItemController extends Controller
{
    protected MenuService $menuService;

    public function __construct(MenuService $menuService, Request $request)
    {
        $this->menuService = $menuService;
    }

    public function createMenuItem(Menu $menu, Request $request) {
        $this->menuService->setUser($request->user());
        $this->menuService->setMenu($menu);
        $create = $this->menuService->createMenuItem($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating app  menu item',
                [],
                $this->menuService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('App Menu created', [], $this->menuService->getResultsService()->getErrors());
    }

    public function updateMenuItem(MenuItem $menuItem, Request $request) {
        $this->menuService->setUser($request->user());
        $this->menuService->setMenuItem($menuItem);
        $update = $this->menuService->updateMenuItem($request->all());
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
    public function deleteMenuItem(MenuItem $menuItem) {
        $this->menuService->setMenuItem($menuItem);
        $delete = $this->menuService->deleteMenuItem();
        if (!$delete) {
            return $this->sendErrorResponse(
                'Error deleting app menu item',
                [],
                $this->menuService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('MenuItem deleted', [], $this->menuService->getResultsService()->getErrors());
    }
}
