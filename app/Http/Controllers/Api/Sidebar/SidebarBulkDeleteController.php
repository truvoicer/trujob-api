<?php

namespace App\Http\Controllers\Api\Sidebar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sidebar\SidebarBulkDeleteRequest;
use App\Repositories\SidebarRepository;
use App\Services\Admin\Sidebar\SidebarService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class SidebarBulkDeleteController extends Controller
{
    public function __construct(
        private SidebarService $sidebarService,
        private SidebarRepository $sidebarRepository
    ) {}

    public function __invoke(SidebarBulkDeleteRequest $request)
    {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);
        if (!$this->sidebarService->deleteBulkSidebars($request->validated('ids'))) {
            return response()->json([
                'message' => 'Sidebars could not be deleted.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'message' => 'Sidebars deleted successfully.'
        ], Response::HTTP_OK);
    }
}
