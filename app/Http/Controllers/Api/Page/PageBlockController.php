<?php

namespace App\Http\Controllers\Api\Page;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\CreatePageBlockRequest;
use App\Http\Requests\Page\CreatePageRequest;
use App\Http\Requests\Page\EditPageBlockRequest;
use App\Http\Requests\Page\EditPageRequest;
use App\Http\Resources\Page\PageBlockResource;
use App\Http\Resources\Page\PageResource;
use App\Models\Block;
use App\Models\Page;
use App\Models\PageBlock;
use App\Repositories\PageBlockRepository;
use App\Services\Page\PageService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class PageBlockController extends Controller
{
    public function __construct(
        private PageService $pageService,
        private PageBlockRepository $pageBlockRepository,
    )
    {
    }

    public function index(Page $page, Request $request) 
    {
        $this->pageBlockRepository->setQuery(
            $page->pageBlocks()
        );
        $this->pageBlockRepository->setPagination(true);
        $this->pageBlockRepository->setOrderByColumn(
            $request->get('sort', 'created_at')
        );
        $this->pageBlockRepository->setOrderByDir(
            $request->get('order', 'desc')
        );
        $this->pageBlockRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->pageBlockRepository->setPage(
            $request->get('page', 1)
        );
        $this->pageBlockRepository->setWith([
            'sidebars' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'roles' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }
        ]);

        return PageBlockResource::collection(
            $this->pageBlockRepository->findMany()
        );
    }

    public function show(Page $page, PageBlock $pageBlock)
    {
        return new PageBlockResource($pageBlock);
    }

    public function store(Page $page, Block $block, CreatePageBlockRequest $request)
    {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);
        if (!$this->pageService->createPageBlock($page, $block, $request->validated())) {
            return response()->json([
                'status' => 'error',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Page block created',
        ]);
    }

    public function update(Page $page, PageBlock $pageBlock, EditPageBlockRequest $request)
    {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);
        if (!$this->pageService->updatePageBlock($pageBlock, $request->validated())) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating page block',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Page block updated',
        ]);
    }

    public function destroy(Page $page, PageBlock $pageBlock, Request $request)
    {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);
        
        if (!$this->pageService->deletePageBlock($pageBlock)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting page block',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Page block deleted',
        ]);
    }

}
