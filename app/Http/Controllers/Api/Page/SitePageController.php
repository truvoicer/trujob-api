<?php

namespace App\Http\Controllers\Api\Page;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\SitePageRequest;
use App\Http\Resources\Page\PageResource;
use App\Services\Page\PageService;
use Illuminate\Http\Request;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class SitePageController extends Controller
{
    public function __construct(
        private PageService $pageService
    )
    {
    }

    public function index(Request $request)
    {
        return [];
    }

    public function view(SitePageRequest $request)
    {
        $page = $this->pageService->getPageByPermalink($request->user(), $request->validated('permalink'));

        if (!$page) {
            return response()->json(['error' => 'Page not found'], 404);
        }

        $page->load(['roles', 'pageBlocks']);

        return new PageResource($page);
    }

}
