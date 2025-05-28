<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppMenu\AppMenuResource;
use App\Models\AppMenu;
use App\Services\Admin\Menu\AppMenuService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppMenuController extends Controller
{
    protected AppMenuService $menuService;

    public function __construct(AppMenuService $menuService, Request $request)
    {
        $this->menuService = $menuService;
    }

    public function show(string $menu) {
        return new AppMenuResource(
            $this->menuService->appMenuFetch($menu)
        );
    }

    public function store(Request $request) {
        $this->menuService->setUser($request->user());
        $create = $this->menuService->createAppMenu($request->all());
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

    public function update(AppMenu $appMenu, Request $request) {
        $this->menuService->setUser($request->user());
        $this->menuService->setAppMenu($appMenu);
        $update = $this->menuService->updateAppMenu($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating appMenu',
                [],
                $this->menuService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('AppMenu updated', [], $this->menuService->getResultsService()->getErrors());
    }
    public function destroy(AppMenu $appMenu) {
        $this->menuService->setAppMenu($appMenu);
        $delete = $this->menuService->deleteAppMenu();
        if (!$delete) {
            return $this->sendErrorResponse(
                'Error deleting appMenu',
                [],
                $this->menuService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('AppMenu deleted', [], $this->menuService->getResultsService()->getErrors());
    }
}
