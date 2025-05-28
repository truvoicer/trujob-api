<?php

namespace App\Http\Controllers\Api\Menu;

use App\Exceptions\MenuNotFoundException;
use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\CreateMenuRequest;
use App\Http\Requests\Menu\EditMenuRequest;
use App\Http\Resources\Menu\MenuResource;
use App\Models\Menu;
use App\Models\Site;
use App\Models\SiteUser;
use App\Repositories\MenuRepository;
use App\Services\Admin\Menu\MenuService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends Controller
{

    public function __construct(
        private MenuService $menuService,
        private MenuRepository $menuRepository
    ) {
    }

    public function index(Request $request)
    {
        $this->menuRepository->setQuery(
            $request->user()->site->menus()
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
            'menuItems' => function ($query) {
                $query->orderBy('order', 'asc');
            },
        ]);
        
        return MenuResource::collection(
            $this->menuRepository->findMany()
        );
    }

    public function show(string $menu, Request $request)
    {
        [$site, $user] = SiteHelper::getCurrentSite();

        if ($user) {
        $this->menuService->setUser($user);
        }
        if ($site) {
            $this->menuService->setSite($site);
        }
        $menu = $this->menuService->menuFetch($menu);
        if (!$menu) {
            throw new MenuNotFoundException('Menu not found');
        }
        $menu->load([
            'menuItems' => function ($query) {
                $query->orderBy('order', 'asc');
            },
        ]);
        return new MenuResource($menu);
    }

    public function store(CreateMenuRequest $request)
    {
        $this->menuService->setUser($request->user()->user);
        $this->menuService->setSite($request->user()->site);
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

    public function update(Menu $menu, EditMenuRequest $request)
    {
        $this->menuService->setUser($request->user()->user);
        $this->menuService->setSite($request->user()->site);
        $update = $this->menuService->updateMenu($menu, $request->validated());
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
    public function destroy(Menu $menu, Request $request)
    {
        $this->menuService->setUser($request->user()->user);
        $this->menuService->setSite($request->user()->site);
        $delete = $this->menuService->deleteMenu($menu);
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
