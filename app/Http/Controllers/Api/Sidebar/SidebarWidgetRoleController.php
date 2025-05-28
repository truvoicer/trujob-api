<?php

namespace App\Http\Controllers\Api\Sidebar;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Services\Admin\Sidebar\SidebarService;
use Illuminate\Http\Request;

class SidebarWidgetRoleController extends Controller
{

    public function __construct(
        private SidebarService $sidebarService
    ) {}

    public function index(
        Sidebar $sidebar,
        SidebarWidget $sidebarWidget,
        Request $request
    ) {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);

        return RoleResource::collection(
            $this->sidebarService->getSidebarRepository()
                ->getRoles($sidebarWidget)

        );
    }
    public function store(
        Sidebar $sidebar,
        SidebarWidget $sidebarWidget,
        Role $role,
        Request $request
    ) {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);
        
        $this->sidebarService->assignRoles(
            $sidebarWidget->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role assigned to sidebar widget.",
        ]);
    }
    public function destroy(
        Sidebar $sidebar,
        SidebarWidget $sidebarWidget,
        Role $role,
        Request $request
    ) {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);

        $this->sidebarService->getSidebarRepository()->detachRoles(
            $sidebarWidget->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role removed from sidebar widget.",
        ]);
    }
}
