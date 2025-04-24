<?php

namespace App\Http\Controllers\Api\Sidebar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sidebar\SidebarWidgetReorderRequest;
use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Services\Admin\Sidebar\SidebarService;

class SidebarWidgetReorderController extends Controller
{

    public function __construct(
        private SidebarService $sidebarService
    ) {}

    public function __invoke(
        Sidebar $sidebar,
        SidebarWidget $sidebarWidget,
        SidebarWidgetReorderRequest $request
    ) {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);
        
        $this->sidebarService->moveSidebarWidget(
            $sidebar,
            $sidebarWidget,
            $request->validated('direction')
        );

        return response()->json([
            'message' => "Sidebar widget moved {$request->validated('direction')}",
        ]);
    }
}
