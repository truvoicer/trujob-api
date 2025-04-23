<?php

namespace App\Http\Controllers\Api\Sidebar;

use App\Exceptions\SidebarNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sidebar\CreateSidebarRequest;
use App\Http\Requests\Sidebar\EditSidebarRequest;
use App\Http\Resources\Sidebar\SidebarResource;
use App\Models\Sidebar;
use App\Services\Admin\Sidebar\SidebarService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SidebarController extends Controller
{
    protected SidebarService $sidebarService;

    public function __construct(SidebarService $sidebarService, Request $request)
    {
        $this->sidebarService = $sidebarService;
    }

    public function index(Request $request) {
        $this->sidebarService->setUser($request->user()->user);
        return SidebarResource::collection(
            $this->sidebarService->getSidebarRepository()->findBySite(
                $request->user()->site
            )
        );
    }
    public function view(Sidebar $sidebar) {
        return new SidebarResource($sidebar);
    }

    public function create(CreateSidebarRequest $request) {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);
        $create = $this->sidebarService->createSidebar(
            $request->validated()
        );
        if (!$create) {
            return response()->json([
                'message' => 'Error creating app sidebar',
                'errors' => $this->sidebarService->getResultsService()->getErrors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Sidebar created',
        ]);
    }

    public function update(Sidebar $sidebar, EditSidebarRequest $request) {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);
        $update = $this->sidebarService->updateSidebar(
            $sidebar, 
            $request->validated()
        );
        if (!$update) {
            return response()->json([
                'message' => 'Error updating app sidebar',
                'errors' => $this->sidebarService->getResultsService()->getErrors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Sidebar updated',
        ]);
    }
    public function destroy(Sidebar $sidebar, Request $request) {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);
        $delete = $this->sidebarService->deleteSidebar($sidebar);
        if (!$delete) {
            return response()->json([
                'message' => 'Error deleting app sidebar',
                'errors' => $this->sidebarService->getResultsService()->getErrors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Sidebar deleted',
        ]);
    }
}
