<?php

namespace App\Http\Controllers\Api\Page\Block\Sidebar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\Block\PageBlockReorderRequest;
use App\Models\Page;
use App\Models\PageBlock;
use App\Services\Page\PageService;

class PageBlockSidebarReorderController extends Controller
{

    public function __construct(
        private PageService $pageService
    ) {}

    public function __invoke(
        Page $page,
        PageBlock $pageBlock,
        PageBlockReorderRequest $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);

        $this->pageService
            ->getPageRepository()
            ->reorderByDirection(
                $pageBlock,
                $page->pageBlocks()->orderBy('order', 'asc'),
                $request->validated('direction')
            );

        return response()->json([
            'message' => "Page block moved {$request->validated('direction')}",
        ]);
    }
}
