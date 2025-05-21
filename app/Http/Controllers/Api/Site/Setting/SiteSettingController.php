<?php

namespace App\Http\Controllers\Api\Site\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\Setting\CreateSiteSettingRequest;
use App\Http\Requests\Site\Setting\EditSiteSettingRequest;
use App\Http\Resources\Site\Setting\SiteSettingResource;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Services\Site\Setting\SiteSettingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class SiteSettingController extends Controller
{
    public function __construct(
        private SiteSettingService $siteService
    )
    {
    }

    public function index(Request $request)
    {
        return SiteSettingResource::collection(SiteSetting::all());
    }

    public function view(Site $site)
    {
        return new SiteSettingResource(
            $site->settings()->first()
        );
    }

    public function update(Site $site, EditSiteSettingRequest $request)
    {
        $this->siteService->setUser($request->user()->user);
        $this->siteService->setSite($request->user()->site);
        $create = $this->siteService->updateSiteSetting($site, $request->validated());
        if (!$create) {
            return response()->json([
                'status' => 'error',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Site setting updated',
        ]);
    }


}
