<?php

namespace App\Http\Controllers\Api\Widget;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Widget;
use App\Models\Role;
use App\Services\Admin\Widget\WidgetService;
use Illuminate\Http\Request;

class WidgetRoleController extends Controller
{

    public function __construct(
        private WidgetService $widgetService
    ) {}

    public function index(
        Widget $widget,
        Request $request
    ) {
        $this->widgetService->setUser($request->user()->user);
        $this->widgetService->setSite($request->user()->site);

        return RoleResource::collection(
            $this->widgetService->getWidgetRepository()
                ->getRoles($widget)

        );
    }
    public function create(
        Widget $widget,
        Role $role,
        Request $request
    ) {
        $this->widgetService->setUser($request->user()->user);
        $this->widgetService->setSite($request->user()->site);
        
        $this->widgetService->assignRoles(
            $widget->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role assigned to widget.",
        ]);
    }
    public function destroy(
        Widget $widget,
        Role $role,
        Request $request
    ) {
        $this->widgetService->setUser($request->user()->user);
        $this->widgetService->setSite($request->user()->site);

        $this->widgetService->getWidgetRepository()->detachRoles(
            $widget->roles(),
            [
                $role->id,
            ],
        );

        return response()->json([
            'message' => "Role removed from widget.",
        ]);
    }
}
