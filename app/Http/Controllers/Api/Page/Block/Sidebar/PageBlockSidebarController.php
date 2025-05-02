<?php

namespace App\Http\Controllers\Api\Page\Block\Sidebar;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Sidebar;
use App\Services\Page\PageService;
use Illuminate\Http\Request;

class PageBlockSidebarController extends Controller
{

    public function __construct(
        private PageService $pageService
    ) {}

    public function create(
        Page $page,
        PageBlock $pageBlock,
        Sidebar $sidebar,
        Request $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);

        $this->pageService
            ->createSidebar(
                $page,
                $pageBlock,
                $sidebar
            );
        return response()->json([
            'message' => 'Sidebar created',
        ]);
    }
}
