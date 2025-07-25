<?php

namespace App\Http\Controllers\Api\Page;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\CreatePageRequest;
use App\Http\Requests\Page\EditPageRequest;
use App\Http\Resources\Page\PageResource;
use App\Models\Page;
use App\Repositories\PageRepository;
use App\Services\Page\PageService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class PageController extends Controller
{
    public function __construct(
        private PageService $pageService,
        private PageRepository $pageRepository
    )
    {
    }

    public function index(Request $request)
    {
        $this->pageRepository->setQuery(
            $request->user()->site->pages()
        );
        $this->pageRepository->setPagination(true);
        $this->pageRepository->setWith([
            'blocks' => function ($query) {
                $query->orderBy('order');
            }
        ]);
        $this->pageRepository->setOrderByColumn(
            $request->get('sort', 'created_at')
        );
        $this->pageRepository->setOrderByDir(
            $request->get('order', 'desc')
        );
        $this->pageRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->pageRepository->setPage(
            $request->get('page', 1)
        );

        return PageResource::collection(
            $this->pageRepository->findMany()
        );
    }

    public function show(Page $page)
    {
        return new PageResource($page);
    }

    public function store(CreatePageRequest $request)
    {
        $this->pageService->setUser($request->user()->user);
        $create = $this->pageService->createPage(
            $request->user()->site,
            $request->validated()
        );
        if (!$create) {
            return response()->json([
                'message' => 'Error creating page',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Page created',
        ]);
    }

    public function update(EditPageRequest $request, Page $page)
    {
        $this->pageService->setUser($request->user()->user);
        $create = $this->pageService->updatePage($page, $request->validated());
        if (!$create) {
            return response()->json([
                'message' => 'Error updating page',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Page updated',
        ]);
    }

    public function destroy(Page $page, Request $request)
    {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);

        if (!$this->pageService->deletePage($page)) {
            return response()->json([
                'message' => 'Error deleting page',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Page deleted',
        ]);
    }

}
