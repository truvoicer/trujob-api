<?php

namespace App\Http\Controllers\Api\Page\Block\Sidebar;

use App\Http\Controllers\Controller;
use App\Http\Resources\Page\PageBlockSidebarResource;
use App\Http\Resources\Sidebar\SidebarResource;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\PageBlockSidebar;
use App\Models\Sidebar;
use App\Repositories\PageBlockRepository;
use App\Services\Page\PageService;
use Illuminate\Http\Request;

class PageBlockSidebarController extends Controller
{

    public function __construct(
        private PageService $pageService,
        private PageBlockRepository $pageBlockRepository
    ) {}

    public function index(
        Page $page,
        PageBlock $pageBlock,
        Request $request
    ) {
        
        $this->pageBlockRepository->setQuery(
            $pageBlock->pageBlockSidebars()
        );
        $this->pageBlockRepository->setPagination(true);
        $this->pageBlockRepository->setSortField(
            $request->get('sort', 'order')
        );
        $this->pageBlockRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->pageBlockRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->pageBlockRepository->setPage(
            $request->get('page', 1)
        );
        $this->pageBlockRepository->setWith([
            'sidebar',
            'pageBlock'
        ]);

        return PageBlockSidebarResource::collection(
            $this->pageBlockRepository->findMany()
        );
    }

    public function create(
        Page $page,
        PageBlock $pageBlock,
        Sidebar $sidebar,
        Request $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);

        $this->pageService
        ->assignSidebars(
            $pageBlock->sidebars(),
            [$sidebar->id],
        );

        return response()->json([
            'message' => 'Sidebar created',
        ]);
    }

    public function destroy(
        Page $page,
        PageBlock $pageBlock,
        PageBlockSidebar $pageBlockSidebar,
        Request $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);

       $pageBlockSidebar->delete();

        return response()->json([
            'message' => 'Sidebar deleted',
        ]);
    }
}
