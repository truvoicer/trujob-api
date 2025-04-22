<?php

namespace App\Http\Controllers\Api\Menu;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\CreateMenuItemRequest;
use App\Http\Requests\Menu\CreateMenuRequest;
use App\Http\Requests\Menu\EditMenuItemRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\Admin\Menu\MenuService;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class MenuItemController extends Controller
{

    public function __construct(
        private MenuService $menuService
    )
    {}

    public function create(Menu $menu, CreateMenuItemRequest $request) {
        $this->menuService->setUser(request()->user()->user);
        $this->menuService->setSite(request()->user()->site);
        $menuRules = (new CreateMenuRequest())->rules();
        unset($menuRules['site_id']);
        $validateNested = ValidationHelpers::nestedValidation(
            $request->validated(),
            [
                'menus' => $menuRules,
                'menu_items' => (new CreateMenuItemRequest())->rules(),
            ],
            3
        );
        if ($validateNested instanceof Validator) {
            return response()->json([
                'message' => 'Error validating app menu item',
                'errors' => $validateNested->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $create = $this->menuService->createMenuItem(
            $menu,
            [
                ...$request->validated(),
                'menus' => $request->validated('menus'),
            ]
        );
        if (!$create) {
            return response()->json([
                'message' => 'Error creating app menu item',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'MenuItem created',
        ]);
    }

    public function update(Menu $menu, MenuItem $menuItem, EditMenuItemRequest $request) {
        $this->menuService->setUser(request()->user()->user);
        $this->menuService->setSite(request()->user()->site);
        $update = $this->menuService->updateMenuItem($menuItem, $request->validated());
        if (!$update) {
            return response()->json([
                'message' => 'Error updating app menu item',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'MenuItem updated',
        ]);
    }
    
    
    public function destroy(Menu $menu, MenuItem $menuItem) {
        $this->menuService->setUser(request()->user()->user);
        $this->menuService->setSite(request()->user()->site);
        $this->menuService->deleteMenuItem($menuItem);
        return response()->json([
            'message' => 'MenuItem deleted',
        ]);
    }
}
