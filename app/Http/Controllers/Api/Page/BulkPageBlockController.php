<?php

namespace App\Http\Controllers\Api\Page;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\BatchDeletePageBlockRequest;
use App\Models\Page;
use App\Services\Page\PageService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class BulkPageBlockController extends Controller
{
    public function __construct(
        private PageService $pageService
    ) {}

    public function destroy(Page $page, BatchDeletePageBlockRequest $request)
    {
        $this->pageService->setUser($request->user()->user);
        $this->pageService->setSite($request->user()->site);

        if (!$this->pageService->deletePageBlocksByIds($page, $request->get('ids'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting page blocks',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Page blocks deleted',
        ]);
    }
}
