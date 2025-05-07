<?php

namespace App\Http\Controllers\Api\Page;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\PageBulkDeleteRequest;
use App\Repositories\PageRepository;
use App\Services\Page\PageService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class PageBulkDeleteController extends Controller
{
    public function __construct(
        private PageService $pageService,
        private PageRepository $pageRepository
    ) {}

    public function __invoke(PageBulkDeleteRequest $request)
    {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);
        if (!$this->pageService->deleteBulkPages($request->validated('ids'))) {
            return response()->json([
                'message' => 'Pages could not be deleted.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'message' => 'Pages deleted successfully.'
        ], Response::HTTP_OK);
    }
}
