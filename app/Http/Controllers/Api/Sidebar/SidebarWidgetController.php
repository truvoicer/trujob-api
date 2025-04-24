<?php

namespace App\Http\Controllers\Api\Sidebar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sidebar\CreateSidebarWidgetRequest;
use App\Http\Requests\Sidebar\EditSidebarWidgetRequest;
use App\Http\Resources\Sidebar\SidebarWidgetResource;
use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Models\Widget;
use App\Services\Admin\Sidebar\SidebarService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SidebarWidgetController extends Controller
{

    public function __construct(
        private SidebarService $sidebarService
    ) {}

    public function index(Sidebar $sidebar, Request $request)
    {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);
        
        return SidebarWidgetResource::collection(
            $this->sidebarService->getSidebarRepository()->findSidebarWidgets(
                $sidebar
            )
        );
    }
    public function view(
        Sidebar $sidebar,
        SidebarWidget $sidebarWidget
    ) {
        return new SidebarWidgetResource($sidebarWidget);
    }

    public function create(Sidebar $sidebar, Widget $widget, CreateSidebarWidgetRequest $request)
    {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);
        $create = $this->sidebarService->createSidebarWidget(
            $sidebar,
            $widget,
            $request->validated()
        );
        if (!$create) {
            return response()->json([
                'message' => 'Error creating app sidebar widget',
                'errors' => $this->sidebarService->getResultsService()->getErrors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Sidebar widget created',
        ]);
    }
    public function update(Sidebar $sidebar, SidebarWidget $sidebarWidget, EditSidebarWidgetRequest $request)
    {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);
        $update = $this->sidebarService->updateSidebarWidget($sidebarWidget, $request->validated());
        if (!$update) {
            return response()->json([
                'message' => 'Error updating app sidebar widget',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Sidebar widget updated',
        ]);
    }

    public function destroy(Sidebar $sidebar, SidebarWidget $sidebarWidget, Request $request)
    {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);
        if (!$this->sidebarService->deleteSidebarWidget($sidebarWidget)) {
            return response()->json([
                'message' => 'Error deleting sidebar widget',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Sidebar widget deleted',
        ]);
    }
}
