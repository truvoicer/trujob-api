<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Models\AppMenu;
use App\Models\AppMenuItem;
use App\Services\Admin\Menu\AppMenuService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppMenuItemController extends Controller
{
    protected AppMenuService $menuService;

    public function __construct(AppMenuService $menuService, Request $request)
    {
        $this->menuService = $menuService;
    }

    public function store(AppMenu $appMenu, Request $request) {
        $this->menuService->setUser($request->user());
        $this->menuService->setAppMenu($appMenu);
        $create = $this->menuService->createAppMenuItem($request->all());
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

    public function update(AppMenuItem $appMenuItem, Request $request) {
        $this->menuService->setUser($request->user());
        $this->menuService->setAppMenuItem($appMenuItem);
        $update = $this->menuService->updateAppMenuItem($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating app menu item',
                [],
                $this->menuService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('AppMenuItem updated', [], $this->menuService->getResultsService()->getErrors());
    }
    public function removeAppMenuItemFromAppMenu(AppMenu $appMenu, AppMenuItem $appMenuItem) {
        $this->menuService->setAppMenu($appMenu);
        $this->menuService->setAppMenuItem($appMenuItem);
        if (!$this->menuService->removeAppMenuItem()) {
            return $this->sendErrorResponse(
                'Error removing app menu item',
                [],
                $this->menuService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('AppMenuItem removed', [], $this->menuService->getResultsService()->getErrors());
    }
    public function destroy(AppMenuItem $appMenuItem) {
        $this->menuService->setAppMenuItem($appMenuItem);
        $delete = $this->menuService->deleteAppMenuItem();
        if (!$delete) {
            return $this->sendErrorResponse(
                'Error deleting app menu item',
                [],
                $this->menuService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('AppMenuItem deleted', [], $this->menuService->getResultsService()->getErrors());
    }
}
