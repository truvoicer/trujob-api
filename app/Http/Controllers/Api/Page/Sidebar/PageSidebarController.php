<?php

namespace App\Http\Controllers\Api\Page\Sidebar;

use App\Http\Controllers\Controller;
use App\Http\Resources\Sidebar\SidebarResource;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Sidebar;
use App\Repositories\PageRepository;
use App\Services\Page\PageService;
use Illuminate\Http\Request;

class PageSidebarController extends Controller
{

    public function __construct(
        private PageService $pageService,
        private PageRepository $pageRepository
    ) {}

    public function index(
        Page $page,
        Request $request,
    ) {
 
        $this->pageRepository->setQuery(
            $page->sidebars()
        );
        $this->pageRepository->setPagination(true);
        $this->pageRepository->setSortField(
            $request->get('sort', 'created_at')
        );
        $this->pageRepository->setOrderDir(
            $request->get('order', 'desc')
        );
        $this->pageRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->pageRepository->setPage(
            $request->get('page', 1)
        );

        return SidebarResource::collection(
            $this->pageRepository->findMany()
        );
    }
    public function create(
        Page $page,
        Sidebar $sidebar,
        Request $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);

        $this->pageService
            ->assignSidebars(
                $page->sidebars(),
                [$sidebar->id],
            );
        return response()->json([
            'message' => 'Sidebar created',
        ]);
    }
    public function destroy(
        Page $page,
        Sidebar $sidebar,
        Request $request
    ) {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);

        $this->pageService
            ->detachSidebars(
                $page->sidebars(),
                [$sidebar->id],
            );
        return response()->json([
            'message' => 'Sidebar deleted',
        ]);
    }
}
