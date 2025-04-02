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
        private PageService $pageService
    )
    {
    }

    public function index(Page $page)
    {
        return PageBlockResource::collection($page->pageBlocks);
    }

    public function view(Page $page, Block $block)
    {
        return new PageBlockResource($page->pageBlocks);
    }

    public function create(Page $page, CreatePageBlockRequest $request)
    {
        $this->pageService->setUser($request->user());
        $this->pageService->setPage($page);
        if (!$this->pageService->createPageBlock($page, $request->validated())) {
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
        $this->pageService->setUser($request->user());
        $this->pageService->setPage($page);
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
        $this->pageService->setUser($request->user());
        $this->pageService->setPage($page);
        
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
