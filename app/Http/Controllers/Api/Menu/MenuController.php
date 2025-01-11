<?php

namespace App\Http\Controllers\Api\Menu;

use App\Exceptions\MenuNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\CreateMenuRequest;
use App\Http\Requests\Menu\EditMenuRequest;
use App\Http\Resources\Menu\MenuResource;
use App\Models\Menu;
use App\Services\Admin\Menu\MenuService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends Controller
{
    protected MenuService $menuService;

    public function __construct(MenuService $menuService, Request $request)
    {
        $this->menuService = $menuService;
    }

    public function fetchMenu(string $menu) {
        $menu = $this->menuService->menuFetch($menu);
        if (!$menu) {
            throw new MenuNotFoundException();
        }
        return new MenuResource($menu);
    }

    public function createMenu(CreateMenuRequest $request) {
        $this->menuService->setUser($request->user());
        $create = $this->menuService->createMenu($request->validated());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating app menu',
                [],
                $this->menuService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('App Menu created', [], $this->menuService->getResultsService()->getErrors());
    }

    public function updateMenu(Menu $menu, EditMenuRequest $request) {
        $this->menuService->setUser($request->user());
        $this->menuService->setMenu($menu);
        $update = $this->menuService->updateMenu($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating menu',
                [],
                $this->menuService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Menu updated', [], $this->menuService->getResultsService()->getErrors());
    }
    public function deleteMenu(Menu $menu) {
        $this->menuService->setMenu($menu);
        $delete = $this->menuService->deleteMenu();
        if (!$delete) {
            return $this->sendErrorResponse(
                'Error deleting menu',
                [],
                $this->menuService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Menu deleted', [], $this->menuService->getResultsService()->getErrors());
    }
}
