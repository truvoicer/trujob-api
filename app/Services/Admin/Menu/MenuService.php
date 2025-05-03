<?php

namespace App\Services\Admin\Menu;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemMenu;
use App\Models\Page;
use App\Repositories\MenuRepository;
use App\Services\BaseService;
use App\Services\ResultsService;
use App\Traits\RoleTrait;

class MenuService extends BaseService
{
    use RoleTrait;

    private Menu $menu;
    private MenuItem $menuItem;

    public function __construct(
        private ResultsService $resultsService,
        private MenuRepository $menuRepository
    ) {
        parent::__construct();
    }

    public function menuFetch(string $menuName)
    {
        $query = Menu::where('name', $menuName);
        if (!isset($this->user)) {
            $query->whereDoesntHave('roles');
        }
        return $query->first();
    }

    public function moveMenuItem(Menu $menu, MenuItem $menuItem, string $direction)
    {
        $this->menuRepository->reorderByDirection(
            $menuItem,
            $menu->menuItems()->orderBy('order', 'asc'),
            $direction
        );
    }

    public function createMenu(array $data)
    {
        $menuItems = [];
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        if (!empty($data['menu_items']) && is_array($data['menu_items'])) {
            $menuItems = $data['menu_items'];
            unset($data['menu_items']);
        }
        $menu = $this->site->menus()->create($data);
        if (!$menu->exists()) {
            throw new \Exception('Error creating menu');
        }

        if (is_array($roles)) {
            $this->syncRoles($menu->roles(), $roles);
        }
        if (empty($menuItems)) {
            return true;
        }
        foreach ($menuItems as $menuItem) {
            $this->createMenuItem($menu, $menuItem);
        }
        if ($this->resultsService->hasErrors()) {
            return false;
        }
        return true;
    }

    public function updateMenu(Menu $menu, array $data)
    {
        $roles = null;
        $menuItems = [];

        if (!empty($data['menu_items']) && is_array($data['menu_items'])) {
            $menuItems = $data['menu_items'];
            unset($data['menu_items']);
        }
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }

        if (!$menu->update($data)) {
            $this->resultsService->addError('Error updating app menu', $data);
            return false;
        }
        if (is_array($roles)) {
            $this->syncRoles($menu->roles(), $roles);
        }

        if (!count($menuItems)) {
            return true;
        }

        $filterCreateMenuItems = array_filter($menuItems, function ($item) {
            return !array_key_exists('id', $item) || empty($item['id']);
        });
        $filterUpdateMenuItems = array_filter($menuItems, function ($item) {
            return !empty($item['id']);
        });
        foreach ($filterCreateMenuItems as $data) {
            $this->createMenuItem($menu, $data);
        }
        foreach ($filterUpdateMenuItems as $data) {
            $menuItem = $menu->menuItems()->find($data['id']);
            if (!$menuItem) {
                throw new \Exception('Menu item not found: ' . $data['id']);
            }
            $this->updateMenuItem($menuItem, $data);
        }
        return true;
    }

    public function deleteMenu(Menu $menu)
    {
        if (!$menu->delete()) {
            $this->resultsService->addError('Error deleting app menu');
            return false;
        }
        return true;
    }
    public function createMenuItem(Menu $menu, array $data)
    {
        $menus = null;
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        if (!empty($data['menus']) && is_array($data['menus'])) {
            $menus = $data['menus'];
            unset($data['menus']);
        }

        if (!empty($data['page_name'])) {
            $page = $this->site->pages()->where('name', $data['page_name'])->first();
            if (!$page) {
                throw new \Exception('Page not found: ' . $data['page']);
            }
            unset($data['page']);
            $data['page_id'] = $page->id;
        }
        if (!array_key_exists('order', $data)) {
            $data['order'] = $this->menuRepository->getHighestOrder($menu->menuItems(), 'menu_items.order');
        }
        $menuItem = $menu->menuItems()->create($data);
        if (!$menuItem->exists()) {
            throw new \Exception('Error creating menu item');
        }
        if (is_array($roles)) {
            $this->syncRoles($menuItem->roles(), $roles);
        }
        if (is_array($menus)) {
            $this->addMenuToMenuItem($menuItem, $menus);
        }
    }

    public function updateMenuItem(MenuItem $menuItem, array $data)
    {
        $roles = null;
        $menus = null;

        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        if (!empty($data['menus']) && is_array($data['menus'])) {
            $menus = $data['menus'];
            unset($data['menus']);
        }

        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }

        if (!empty($data['page_name'])) {
            $page = $this->site->pages()->where('name', $data['page_name'])->first();
            if (!$page) {
                throw new \Exception('Page not found: ' . $data['page']);
            }
            unset($data['page_name']);
            $data['page_id'] = $page->id;
        }

        if (!$menuItem->update($data)) {
            throw new \Exception('Error updating menu item');
        }

        if (is_array($roles)) {
            $this->syncRoles($menuItem->roles(), $roles);
        }
        if (is_array($menus)) {
            $this->addMenuToMenuItem($menuItem, $menus);
        }
        return true;
    }

    public function addMenuToMenuItem(MenuItem $menuItem, array $menus)
    {
        $menus = array_map(function ($menu) {
            if (is_string($menu)) {
                $menu = $this->site->menus()->where('name', $menu)->first();
                if (!$menu) {
                    throw new \Exception('Menu not found | name: ' . $menu);
                }
                return $menu->id;
            }
            if (is_int($menu)) {
                return $menu;
            }
            throw new \Exception('Invalid menu ID/Name: ' . $menu);
        }, $menus);
        $menuItem->menus()->sync($menus);
    }

    public function removeMenuItem(Menu $menu, MenuItem $menuItem)
    {
        if (!$menu->menuItems()->delete($menuItem)) {
            $this->resultsService->addError('Error deleting app menu item');
            return false;
        }
        return true;
    }
    public function deleteMenuItem(MenuItem $menuItem): void
    {
        if (!$menuItem->delete()) {
            throw new \Exception('Error deleting menu item');
        }
    }
    public function deleteMenuItemMenu(MenuItemMenu $menuItemMenu): void
    {
        if (!$menuItemMenu->delete()) {
            throw new \Exception('Error deleting menu item');
        }
    }

    /**
     * @return ResultsService
     */
    public function getResultsService(): ResultsService
    {
        return $this->resultsService;
    }

    public function getMenuRepository(): MenuRepository
    {
        return $this->menuRepository;
    }
}
