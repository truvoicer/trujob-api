<?php

namespace App\Services\Admin\Sidebar;

use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Repositories\SidebarRepository;
use App\Services\BaseService;
use App\Services\ResultsService;
use App\Traits\RoleTrait;

class SidebarService extends BaseService
{
    use RoleTrait;

    private Sidebar $sidebar;
    private SidebarWidget $sidebarWidget;

    public function __construct(
        private ResultsService $resultsService, 
        private SidebarRepository $sidebarRepository
    ){
        parent::__construct();
    }

    public function sidebarFetch(string $sidebarName) {
        return Sidebar::where('name', $sidebarName)->first();
    }

    public function createSidebar(array $data) {
        $sidebarWidgets = [];
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        if (!empty($data['widgets']) && is_array($data['widgets'])) {
            $sidebarWidgets = $data['widgets'];
            unset($data['widgets']);
        }
        $sidebar = new Sidebar($data);
        if (!$sidebar->save()) {
            $this->resultsService->addError('Error adding app sidebar', $data);
            return false;
        }
        if (is_array($roles)) {
            $this->syncRoles($sidebar->roles(), $roles);
        }
        if (empty($sidebarWidgets)) {
            return true;
        }
        foreach ($sidebarWidgets as $sidebarWidget) {
            $this->createSidebarWidget($sidebar, $sidebarWidget);
        }
        if ($this->resultsService->hasErrors()) {
            return false;
        }
        return true;
    }

    public function updateSidebar(array $data) {
        $roles = null;
        $sidebarWidgets = [];

        if (!empty($data['widgets']) && is_array($data['widgets'])) {
            $sidebarWidgets = $data['widgets'];
            unset($data['widgets']);
        }
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        
        if (!$this->sidebar->update($data)) {
            $this->resultsService->addError('Error updating app sidebar', $data);
            return false;
        }
        if (is_array($roles)) {
            $this->syncRoles($this->sidebar->roles(), $roles);
        }

        if (!count($sidebarWidgets)) {
            return true;
        }
        $this->sidebar->widgets()->delete();
        foreach ($sidebarWidgets as $sidebarWidget) {
            $this->createSidebarWidget($this->sidebar, $sidebarWidget);
        }
        return true;
    }

    public function deleteSidebar() {
        if (!$this->sidebar->delete()) {
            $this->resultsService->addError('Error deleting app sidebar');
            return false;
        }
        return true;
    }
    public function createSidebarWidget(Sidebar $sidebar, array $data) {
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }

        if (!array_key_exists('order', $data)) {
            $data['order'] = $this->sidebarRepository->getHighestOrder($sidebar->widgets());
        }
        $sidebarWidget = $sidebar->widgets()->create($data);
        if (!$sidebar->widgets()->save($sidebarWidget)) {
            $this->resultsService->addError('Error adding sidebar item', $data);
            return false;
        }
        if (is_array($roles)) {
            $this->syncRoles($sidebarWidget->roles(), $roles);
        }
        return true;
    }

    public function updateSidebarWidget(SidebarWidget $sidebarWidget, array $data) {
        $this->sidebarWidget = $sidebarWidget;
        
        $roles = null;
        
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        
        if (!$this->sidebarWidget->update($data)) {
            $this->resultsService->addError('Error updating app sidebar item', $data);
            return false;
        }

        if (is_array($roles)) {
            $this->syncRoles($this->sidebarWidget->roles(), $roles);
        }
        return true;
    }

    public function removeSidebarWidget() {
        if (!$this->sidebar->sidebarWidgets()->delete($this->sidebarWidget)) {
            $this->resultsService->addError('Error deleting app sidebar item');
            return false;
        }
        return true;
    }
    public function deleteSidebarWidget() {
        if (!$this->sidebarWidget->delete()) {
            $this->resultsService->addError('Error deleting app sidebar item');
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
     * @param Sidebar $sidebar
     */
    public function setSidebar(Sidebar $sidebar): void
    {
        $this->sidebar = $sidebar;
    }

    /**
     * @param SidebarWidget $sidebarWidget
     */
    public function setSidebarWidget(SidebarWidget $sidebarWidget): void
    {
        $this->sidebarWidget = $sidebarWidget;
    }


}
