<?php

namespace App\Services\Admin\Sidebar;

use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Models\Site;
use App\Models\Widget;
use App\Repositories\SidebarRepository;
use App\Services\Admin\Widget\WidgetService;
use App\Services\BaseService;
use App\Services\ResultsService;
use App\Traits\RoleTrait;
use Illuminate\Support\Str;

class SidebarService extends BaseService
{
    use RoleTrait;

    public function __construct(
        private ResultsService $resultsService, 
        private SidebarRepository $sidebarRepository,
        private WidgetService $widgetService
    ){
        parent::__construct();
    }
    
    public function sidebarFetch(string $sidebarName) {
        return Sidebar::where('name', $sidebarName)->first();
    }

    public function moveSidebarWidget(Sidebar $sidebar, SidebarWidget $sidebarWidget, string $direction)
    {
        $this->sidebarRepository->reorderByDirection(
            $sidebarWidget,
            $sidebar->sidebarWidgets()->orderBy('order', 'asc'),
            $direction
        );
    }

    public function createSidebar(array $data) {
        if (empty($data['name'])) {
            $data['name'] = Str::slug($data['title']);
        }
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
        $sidebar = $this->site->sidebars()->create($data);
        if (!$sidebar->exists()) {
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
            $findWidget = $this->widgetService->widgetFetch($this->site, $sidebarWidget['name']);
            if (!$findWidget) {
                throw new \Exception('Widget not found: ' . $sidebarWidget['name']);
            }
            $this->createSidebarWidget($sidebar, $findWidget, $sidebarWidget);
        }
        if ($this->resultsService->hasErrors()) {
            return false;
        }
        return true;
    }

    public function updateSidebar(Sidebar $sidebar, array $data) {
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
        
        if (!$sidebar->update($data)) {
            $this->resultsService->addError('Error updating app sidebar', $data);
            return false;
        }
        if (is_array($roles)) {
            $this->syncRoles($sidebar->roles(), $roles);
        }

        if (!count($sidebarWidgets)) {
            return true;
        }
        $sidebar->widgets()->delete();
        foreach ($sidebarWidgets as $sidebarWidget) {
            $findWidget = $this->widgetService->widgetFetch($this->site, $sidebarWidget['name']);
            if (!$findWidget) {
                throw new \Exception('Widget not found: ' . $sidebarWidget['name']);
            }
            $this->createSidebarWidget($sidebar, $findWidget, $sidebarWidget);
        }
        return true;
    }

    public function deleteSidebar(Sidebar $sidebar) {
        if (!$sidebar->delete()) {
            $this->resultsService->addError('Error deleting app sidebar');
            return false;
        }
        return true;
    }
    public function createSidebarWidget(Sidebar $sidebar, Widget $widget, array $data) {
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }

        if (!array_key_exists('order', $data)) {
            $data['order'] = $this->sidebarRepository->getHighestOrder($sidebar->widgets());
        }
        if (!empty($data['name'])) {
            unset($data['name']);
        }
        $sidebarWidget = SidebarWidget::create([
            'sidebar_id' => $sidebar->id,
            'widget_id' => $widget->id,
            ...$data
        ]);

        if (is_array($roles)) {
            $this->syncRoles($sidebarWidget->roles(), $roles);
        }
        return true;
    }

    public function updateSidebarWidget(SidebarWidget $sidebarWidget, array $data) {
        $roles = null;
        
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        
        if (!$sidebarWidget->update($data)) {
            $this->resultsService->addError('Error updating app sidebar item', $data);
            return false;
        }

        if (is_array($roles)) {
            $this->syncRoles($sidebarWidget->roles(), $roles);
        }
        return true;
    }

    public function removeSidebarWidget(Sidebar $sidebar, SidebarWidget $sidebarWidget) {
        if (!$sidebar->sidebarWidgets()->delete($sidebarWidget)) {
            $this->resultsService->addError('Error deleting app sidebar item');
            return false;
        }
        return true;
    }
    public function deleteSidebarWidget(SidebarWidget $sidebarWidget) {
        if (!$sidebarWidget->delete()) {
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


    public function getSidebarRepository(): SidebarRepository
    {
        return $this->sidebarRepository;
    }
}
