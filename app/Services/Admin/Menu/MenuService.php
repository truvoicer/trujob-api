<?php

namespace App\Services\Admin\Menu;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\BaseService;
use App\Services\ResultsService;

class MenuService extends BaseService
{
    private ResultsService $resultsService;

    private Menu $menu;
    private MenuItem $menuItem;

    public function __construct(ResultsService $resultsService)
    {
        parent::__construct();
        $this->resultsService = $resultsService;
    }

    public function menuFetch(string $menuName) {
        return Menu::where('name', $menuName)->first();
    }

    public function createMenu(array $data) {
        $this->menu = new Menu($data);
        if (!$this->menu->save()) {
            $this->resultsService->addError('Error adding app menu', $data);
            return false;
        }
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

    public function updateMenu(array $data) {
        $this->menu->fill($data);
        if (!$this->menu->save()) {
            $this->resultsService->addError('Error updating app menu', $data);
            return false;
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
        $this->menuItem = $menu->menuItems()->create($data);
        if (!$this->menu->menuItems()->save($this->menuItem)) {
            $this->resultsService->addError('Error adding app menu item', $data);
            return false;
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
        $this->menuItem->fill($data);
        if (!$this->menuItem->save()) {
            $this->resultsService->addError('Error updating app menu item', $data);
            return false;
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
