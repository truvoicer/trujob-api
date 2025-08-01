<?php

namespace App\Services\Admin\Menu;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemMenu;
use App\Models\Page;
use App\Models\Site;
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

    public function moveMenuItemMenu(MenuItem $menuItem, MenuItemMenu $menuItemMenu, string $direction)
    {
        $this->menuRepository->reorderByDirection(
            $menuItemMenu,
            $menuItem->menuItemMenus()->orderBy('order', 'asc'),
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

        if (array_key_exists('menu_items', $data) && is_array($data['menu_items'])) {
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
        return true;
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

    public function menuExistsInParents(MenuItem $menuItem, Menu $menu): bool
    {
        $parentMenus = $menuItem->parentMenus()->where('menus.id', $menu->id)->get();
        if ($parentMenus->count() > 0) {
            return true;
        }
        foreach ($parentMenus as $parentMenu) {
            $parentMenuItems = $parentMenu->menuItems()->get();
            if ($parentMenuItems->count() === 0) {
                continue;
            }
            foreach ($parentMenuItems as $parentMenuItem) {
                if ($this->menuExistsInParents($parentMenuItem, $menu)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function addMenuToMenuItem(MenuItem $menuItem, array $menus): void
    {
        $menuIds = [];
        foreach ($menus as $menu) {
            if (is_string($menu)) {
                $menu = $this->site->menus()->where('name', $menu)->first();
                if (!$menu) {
                    throw new \Exception('Menu not found | name: ' . $menu);
                }
            } else if (is_int($menu)) {
                $menu = $this->site->menus()->find($menu);
                if (!$menu) {
                    throw new \Exception('Menu not found | id: ' . $menu);
                }
            } else {
                throw new \Exception('Invalid menu ID/Name: ' . $menu);
            }

            if ($this->menuExistsInParents($menuItem, $menu)) {
                throw new \Exception('Menu already exists in parent menu');
            }
            $menuIds[] = $menu->id;
        }

        $findMenuInMenuItem = $menuItem->menus()->whereIn('menus.id', $menuIds)->get();
        $menuIds = array_diff($menuIds, $findMenuInMenuItem->pluck('id')->toArray());
        if (count($menuIds) > 0) {
            $menuItem->menus()->attach($menuIds);
        }
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

    public function deleteBulkMenus(array $ids)
    {
        if (empty($ids)) {
            return false;
        }
        $menus = $this->site->menus()->whereIn('id', $ids)->get();
        foreach ($menus as $menu) {
            if (!$this->deleteMenu($menu)) {
                throw new \Exception('Error deleting menu');
            }
        }
        return true;
    }

    public function defaultMenus()
    {
        $data = include_once(database_path('data/MenuData.php'));
        if (!$data) {
            throw new \Exception('Error reading MenuData.php file ' . database_path('data/MenuData.php'));
        }
        $newNames = array_column($data, 'name');
        $existingNames = Menu::all()->pluck('name')->toArray();
        //Delete existing menus that are not in the new data
        $menusToDelete = array_diff($existingNames, $newNames);
        if (count($menusToDelete) > 0) {
            Menu::whereIn('name', $menusToDelete)->delete();
        }
        foreach ($data as $index => $menu) {
            $site = Site::where('name', $menu['site'])->first();
            if (!$site) {
                throw new \Exception('Site not found: ' . $menu['site']);
            }
            unset($menu['site']);
            $this->setSite($site);
            $menu['site_id'] = $site->id;
            if (!empty($menu['menu_items']) && is_array($menu['menu_items'])) {
                $menu['menu_items'] = array_map(function ($item) use ($site) {
                    if (!empty($item['page_name'])) {
                        $page = Page::where('name', $item['page_name'])->where('site_id', $site->id)->first();
                        if (!$page) {
                            throw new \Exception('Page not found: ' . $item['page_name']);
                        }
                        unset($item['page_name']);
                        $item['page_id'] = $page->id;
                    }
                    return $item;
                }, $menu['menu_items']);
            }
            $getMenu = Menu::where('name', $menu['name'])->where('site_id', $site->id)->first();
            if (!$getMenu) {
                if (!$this->createMenu($menu)) {
                    throw new \Exception('Error creating menu: ' . $index);
                }
                continue;
            }
            $existingMenuItems = [];
            $newMenuItems = [];

            if (!empty($menu['menu_items']) && is_array($menu['menu_items'])) {
                // Filter out menu items that already exist in the menu
                $newMenuItems = array_filter(
                    $menu['menu_items'],
                    function ($item) use ($getMenu, &$existingMenuItems) {
                        $query = $getMenu->menuItems();
                        if (array_key_exists('url', $item)) {
                            $query->where('url', $item['url']);
                        }
                        if (array_key_exists('label', $item)) {
                            $query->where('label', $item['label']);
                        }
                        if (array_key_exists('page_id', $item)) {
                            $query->where('page_id', $item['page_id']);
                        }
                        $getMenuItem = $query->first();
                        if ($getMenuItem) {
                            $existingMenuItems[] = [
                                'id' => $getMenuItem->id,
                                ...$item
                            ];
                            return false;
                        }
                        return true;
                    },
                    ARRAY_FILTER_USE_BOTH
                );
            }

            $deletableMenuItems = $getMenu->menuItems()
            ->whereNotIn('menu_items.id', array_column($existingMenuItems, 'id'))
            ->get();

            foreach ($deletableMenuItems as $menuItem) {
                $this->deleteMenuItem($menuItem);
            }
            $menu['menu_items'] = $newMenuItems;
            if (!$this->updateMenu($getMenu, $menu)) {
                throw new \Exception('Error updating menu: ' . $index);
            }

            foreach ($existingMenuItems as $item) {
                if (array_key_exists('id', $item) && !empty($item['id'])) {
                    $menuItem = $getMenu->menuItems()->find($item['id']);
                    if (!$menuItem) {
                        throw new \Exception('Menu item not found: ' . $item['id']);
                    }
                    if (!$this->updateMenuItem($menuItem, $item)) {
                        throw new \Exception('Error updating menu item: ' . $item['id']);
                    }
                } else {
                    throw new \Exception('Menu item ID is required for existing menu items: ' . json_encode($item));
                }
            }
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
