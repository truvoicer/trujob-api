<?php

namespace App\Services\Admin\Widget;

use App\Models\Widget;
use App\Repositories\WidgetRepository;
use App\Services\BaseService;
use App\Services\ResultsService;
use App\Traits\RoleTrait;

class WidgetService extends BaseService
{
    use RoleTrait;

    private Widget $widget;

    public function __construct(
        private ResultsService $resultsService, 
        private WidgetRepository $widgetRepository
    ){
        parent::__construct();
    }

    public function widgetFetch(string $widgetName) {
        return Widget::where('name', $widgetName)->first();
    }

    public function createWidget(array $data) {
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        $widget = new Widget($data);
        if (!$widget->save()) {
            $this->resultsService->addError('Error adding app widget', $data);
            return false;
        }
        if (is_array($roles)) {
            $this->syncRoles($widget->roles(), $roles);
        }
        if ($this->resultsService->hasErrors()) {
            return false;
        }
        return true;
    }

    public function updateWidget(array $data) {
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        
        if (!$this->widget->update($data)) {
            $this->resultsService->addError('Error updating app widget', $data);
            return false;
        }
        if (is_array($roles)) {
            $this->syncRoles($this->widget->roles(), $roles);
        }

        return true;
    }

    public function deleteWidget() {
        if (!$this->widget->delete()) {
            $this->resultsService->addError('Error deleting app widget');
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
     * @param Widget $widget
     */
    public function setWidget(Widget $widget): void
    {
        $this->widget = $widget;
    }



}
