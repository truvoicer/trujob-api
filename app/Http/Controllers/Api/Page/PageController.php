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
        
        return PageResource::collection(
            $this->pageRepository->findMany()
        );
    }

    public function view(Page $page)
    {
        return new PageResource($page);
    }

    public function create(CreatePageRequest $request)
    {
        $this->pageService->setUser($request->user());
        $create = $this->pageService->createPage(
            $request->user()->site,
            $request->validated()
        );
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating page',
                [],
                $this->pageService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Page created', [], $this->pageService->getResultsService()->getErrors());
    }

    public function update(EditPageRequest $request, Page $page)
    {
        $this->pageService->setUser($request->user()->user);
        $create = $this->pageService->updatePage($page, $request->validated());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error updating page',
                [],
                $this->pageService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Page updated', [], $this->pageService->getResultsService()->getErrors());
    }

    public function delete(Page $page, Request $request)
    {
        $this->pageService->setUser($request->user());
        $delete = $this->pageService->deletePage($page);
        if (!$delete) {
            return $this->sendErrorResponse(
                'Error deleting page',
                [],
                $this->pageService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Page deleted', [], $this->pageService->getResultsService()->getErrors());
    }

}
