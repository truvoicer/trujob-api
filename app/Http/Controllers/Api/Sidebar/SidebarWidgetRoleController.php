<?php

namespace App\Http\Controllers\Api\Sidebar;

use App\Http\Controllers\Controller;
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

    public function create(
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
}
