<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\CreateSiteRequest;
use App\Http\Requests\Site\EditSiteRequest;
use App\Http\Resources\Site\SiteResource;
use App\Models\Site;
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
        return SiteResource::collection(Site::all());
    }

    public function view(Site $site)
    {
        return new SiteResource($site);
    }

    public function create(CreateSiteRequest $request)
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

    public function update(EditSiteRequest $request, Site $site)
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
