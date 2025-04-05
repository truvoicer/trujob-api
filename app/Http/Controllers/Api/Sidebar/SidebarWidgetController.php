<?php

namespace App\Http\Controllers\Api\Sidebar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sidebar\CreateSidebarWidgetRequest;
use App\Http\Requests\Sidebar\EditSidebarWidgetRequest;
use App\Http\Resources\Sidebar\SidebarWidgetResource;
use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Services\Admin\Sidebar\SidebarService;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class SidebarWidgetController extends Controller
{

    public function __construct(
        private SidebarService $sidebarService
    )
    {}

    public function view(SidebarWidget $sidebarWidget) {
        return new SidebarWidgetResource($sidebarWidget);
    }

    public function create(Sidebar $sidebar, CreateSidebarWidgetRequest $request) {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSidebar($sidebar);
        $create = $this->sidebarService->createSidebarWidget($sidebar, $request->validated());
        if (!$create) {
            return response()->json([
                'message' => 'Error creating app sidebar widget',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Sidebar created',
        ]);
    }

    public function update(Sidebar $sidebar, SidebarWidget $sidebarWidget, EditSidebarWidgetRequest $request) {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSidebar($sidebar);
        $update = $this->sidebarService->updateSidebarWidget($sidebarWidget, $request->validated());
        if (!$update) {
            return response()->json([
                'message' => 'Error updating app sidebar item',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Sidebar updated',
        ]);
    }
    
    public function destroy(Sidebar $sidebar, SidebarWidget $sidebarWidget) {
        $this->sidebarService->setSidebar($sidebar);
        $this->sidebarService->setSidebarWidget($sidebarWidget);
        if (!$this->sidebarService->deleteSidebarWidget()) {
            return response()->json([
                'message' => 'Error deleting app sidebar item',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Sidebar deleted',
        ]);
    }
}
