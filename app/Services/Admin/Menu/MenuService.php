<?php

namespace App\Services\Admin\Menu;

use App\Models\Menu;
use App\Models\MenuItem;
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
    ){
        parent::__construct();
    }

    public function menuFetch(string $menuName) {
        return Menu::where('name', $menuName)->first();
    }

    public function createMenu(array $data) {
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
        $menu = new Menu($data);
        if (!$menu->save()) {
            $this->resultsService->addError('Error adding app menu', $data);
            return false;
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

    public function updateMenu(array $data) {
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        
        if (!$this->menu->update($data)) {
            $this->resultsService->addError('Error updating app menu', $data);
            return false;
        }
        if (is_array($roles)) {
            $this->syncRoles($this->menu->roles(), $roles);
        }
        return true;
    }

    public function deleteMenu() {
        if (!$this->menu->delete()) {
            $this->resultsService->addError('Error deleting app menu');
            return false;
        }
        return true;
    }
    public function createMenuItem(Menu $menu, array $data) {
        $menus = [];
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        if (!empty($data['menus']) && is_array($data['menus'])) {
            $menus = $data['menus'];
            unset($data['menus']);
        }

        if (!empty($data['page'])) {
            $page = Page::where('name', $data['page'])->first();
            if (!$page) {
                throw new \Exception('Page not found: ' . $data['page']);
            }
            unset($data['page']);
            $data['page_id'] = $page->id;
        }
        if (!array_key_exists('order', $data)) {
            $data['order'] = $this->menuRepository->getHighestOrder($menu->menuItems());
        }
        $menuItem = $menu->menuItems()->create($data);
        if (!$menu->menuItems()->save($menuItem)) {
            $this->resultsService->addError('Error adding menu item', $data);
            return false;
        }
        if (is_array($roles)) {
            $this->syncRoles($menuItem->roles(), $roles);
        }
        foreach ($menus as $subMenu) {
            $subMenu['menu_item_id'] = $menuItem->id;
            $subMenu['site_id'] = $menu->site->id;
            $this->createMenu($subMenu);
        }
        return true;
    }

    public function addMenuToMenuItem(MenuItem $menuItem, array $data) {
        $this->menuItem = $menuItem;
        $this->menu = $this->menuItem->menus()->create($data);

        if (empty($data['menu_items'])) {
            return true;
        }

        foreach ($data['menu_items'] as $menuItem) {
            $this->createMenuItem($this->menu, $menuItem);
        }
        if ($this->resultsService->hasErrors()) {
            return false;
        }
        return true;
    }
    public function updateMenuItem(MenuItem $menuItem, array $data) {
        $this->menuItem = $menuItem;
        
        $roles = null;
        
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        
        if (!$this->menuItem->update($data)) {
            $this->resultsService->addError('Error updating app menu item', $data);
            return false;
        }

        if (is_array($roles)) {
            $this->syncRoles($this->menuItem->roles(), $roles);
        }
        return true;
    }

    public function removeMenuItem() {
        if (!$this->menu->menuItems()->delete($this->menuItem)) {
            $this->resultsService->addError('Error deleting app menu item');
            return false;
        }
        return true;
    }
    public function deleteMenuItem() {
        if (!$this->menuItem->delete()) {
            $this->resultsService->addError('Error deleting app menu item');
            return false;
        }
        return true;
    }

    /**
     * @return ResultsService
     */
    public function getResultsService(): ResultsService
    {
        return $this->resultsService;
    }

    /**
     * @param Menu $menu
     */
    public function setMenu(Menu $menu): void
    {
        $this->menu = $menu;
    }

    /**
     * @param MenuItem $menuItem
     */
    public function setMenuItem(MenuItem $menuItem): void
    {
        $this->menuItem = $menuItem;
    }


}
