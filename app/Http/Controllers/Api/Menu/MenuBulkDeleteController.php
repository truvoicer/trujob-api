<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuBulkDeleteRequest;
use App\Repositories\MenuRepository;
use App\Services\Admin\Menu\MenuService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class MenuBulkDeleteController extends Controller
{
    public function __construct(
        private MenuService $menuService,
        private MenuRepository $menuRepository
    ) {}

    public function __invoke(MenuBulkDeleteRequest $request)
    {
        $this->menuService->setUser($request->user()->user);
        $this->menuService->setSite($request->user()->site);
        if (!$this->menuService->deleteBulkMenus($request->validated('ids'))) {
            return response()->json([
                'message' => 'Menus could not be deleted.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'message' => 'Menus deleted successfully.'
        ], Response::HTTP_OK);
    }
}
