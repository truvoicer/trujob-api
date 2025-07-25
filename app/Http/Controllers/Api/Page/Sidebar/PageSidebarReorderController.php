<?php

namespace App\Http\Controllers\Api\Page\Sidebar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\Block\PageBlockReorderRequest;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\PageSidebar;
use App\Models\Sidebar;
use App\Services\Page\PageService;

class PageSidebarReorderController extends Controller
{

    public function __construct(
        private PageService $pageService
    ) {}

    public function update(
        Page $page,
        Sidebar $sidebar,
        PageBlockReorderRequest $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);
        $pageSidebar = $page->pageSidebars()
            ->where('sidebar_id', $sidebar->id)
            ->first();
        if (!$pageSidebar) {
            return response()->json(['message' => 'Sidebar not found'], 404);
        }
        $this->pageService
            ->getPageRepository()
            ->reorderByDirection(
                $pageSidebar,
                $page->pageSidebars()->orderBy('order', 'asc'),
                $request->validated('direction')
            );

        return response()->json([
            'message' => "Sidebar moved {$request->validated('direction')}",
        ]);
    }
}
