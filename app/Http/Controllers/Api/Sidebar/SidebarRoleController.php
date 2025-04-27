<?php

namespace App\Http\Controllers\Api\Sidebar;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Models\Sidebar;
use App\Services\Admin\Sidebar\SidebarService;
use Illuminate\Http\Request;

class SidebarRoleController extends Controller
{

    public function __construct(
        private SidebarService $sidebarService
    ) {}

    public function index(
        Sidebar $sidebar,
        Request $request
    ) {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);

        return RoleResource::collection(
            $this->sidebarService->getSidebarRepository()
                ->getRoles($sidebar)

        );
    }
    public function create(
        Sidebar $sidebar,
        Role $role,
        Request $request
    ) {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);
        
        $this->sidebarService->assignRoles(
            $sidebar->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role assigned to sidebar.",
        ]);
    }
    public function destroy(
        Sidebar $sidebar,
        Role $role,
        Request $request
    ) {
        $this->sidebarService->setUser($request->user()->user);
        $this->sidebarService->setSite($request->user()->site);

        $this->sidebarService->getSidebarRepository()->detachRoles(
            $sidebar->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role removed from sidebar.",
        ]);
    }
}
