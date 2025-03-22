<?php

namespace App\Http\Controllers\Api\Page;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\CreatePageRequest;
use App\Http\Requests\Page\EditPageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Models\Site;
use App\Services\Page\PageService;
use App\Services\Site\SiteService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class SiteController extends Controller
{
    public function __construct(
        private SiteService $siteService
    )
    {
    }

    public function index(Request $request)
    {
        return [];
    }

    public function view(Page $page)
    {
        return new PageResource($page);
    }

    public function create(CreatePageRequest $request)
    {
        $this->siteService->setUser($request->user());
        $create = $this->siteService->createSite($request->validated());
        if (!$create) {
            return response()->json([
                'status' => 'error',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Site created',
        ]);
    }

    public function update(EditPageRequest $request, Site $site)
    {
        $this->siteService->setUser($request->user());
        $create = $this->siteService->updateSite($site, $request->validated());
        if (!$create) {
            return response()->json([
                'status' => 'error',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Site updated',
        ]);
    }

    public function delete(Site $site, Request $request)
    {
        $this->siteService->setUser($request->user());
        if (!$this->siteService->deleteSite($site)) {
            return response()->json([
                'status' => 'error',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Site deleted',
        ]);
    }

}
