<?php

namespace App\Services\Admin\Menu;

use App\Models\AppMenu;
use App\Models\AppMenuItem;
use App\Services\BaseService;
use App\Services\ResultsService;

class MenuService extends BaseService
{
    private ResultsService $resultsService;

    private AppMenu $appMenu;
    private AppMenuItem $appMenuItem;

    public function __construct(ResultsService $resultsService)
    {
        $this->resultsService = $resultsService;
    }

    public function appMenuFetch() {
        return AppMenu::get();
    }

    public function createAppMenu(array $data) {
        $this->appMenu = new AppMenu($data);
        if (!$this->appMenu->save()) {
            $this->resultsService->addError('Error adding app menu', $data);
            return false;
        }
        return true;
    }

    public function updateAppMenu(array $data) {
        $this->appMenu->fill($data);
        if (!$this->appMenu->save()) {
            $this->resultsService->addError('Error updating app menu', $data);
            return false;
        }
        return true;
    }

    public function deleteAppMenu() {
        if (!$this->appMenu->delete()) {
            $this->resultsService->addError('Error deleting app menu');
            return false;
        }
        return true;
    }
    public function createAppMenuItem(array $data) {
        $this->appMenuItem = new AppMenuItem($data);
        if (!$this->appMenu->menuItems()->save($this->appMenuItem)) {
            $this->resultsService->addError('Error adding app menu item', $data);
            return false;
        }
        return true;
    }

    public function updateAppMenuItem(array $data) {
        $this->appMenuItem->fill($data);
        if (!$this->appMenuItem->save()) {
            $this->resultsService->addError('Error updating app menu item', $data);
            return false;
        }
        return true;
    }

    public function removeAppMenuItem() {
        if (!$this->appMenu->menuItems()->delete($this->appMenuItem)) {
            $this->resultsService->addError('Error deleting app menu item');
            return false;
        }
        return true;
    }
    public function deleteAppMenuItem() {
        if (!$this->appMenuItem->delete()) {
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
     * @param AppMenu $appMenu
     */
    public function setAppMenu(AppMenu $appMenu): void
    {
        $this->appMenu = $appMenu;
    }

    /**
     * @param AppMenuItem $appMenuItem
     */
    public function setAppMenuItem(AppMenuItem $appMenuItem): void
    {
        $this->appMenuItem = $appMenuItem;
    }


}
