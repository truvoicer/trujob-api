<?php

namespace App\Services\Admin\Widget;

use App\Models\Site;
use App\Models\Widget;
use App\Repositories\WidgetRepository;
use App\Services\BaseService;
use App\Services\ResultsService;
use App\Traits\RoleTrait;
use Illuminate\Support\Str;

class WidgetService extends BaseService
{
    use RoleTrait;

    private Widget $widget;

    public function __construct(
        private ResultsService $resultsService,
        private WidgetRepository $widgetRepository
    ) {
        parent::__construct();
    }

    public function widgetFetch(Site $site, string $widgetName): ?Widget
    {
        return $site->widgets()->where('name', $widgetName)->first();
    }

    public function createWidget(Site $site, array $data)
    {
        if (empty($data['name'])) {
            $data['name'] = Str::slug($data['title']);
        }
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }
        $widget = $site->widgets()->create($data);
        if (!$widget->exists()) {
            throw new \Exception('Error adding app widget');
        }
        if (is_array($roles)) {
            $this->syncRoles($widget->roles(), $roles);
        }
        if ($this->resultsService->hasErrors()) {
            return false;
        }
        return true;
    }

    public function updateWidget(Widget $widget, array $data)
    {
        $roles = null;
        if (array_key_exists('roles', $data) && is_array($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);
        }

        if (!$widget->update($data)) {
            throw new \Exception('Error updating app widget');
        }
        if (is_array($roles)) {
            $this->syncRoles($widget->roles(), $roles);
        }

        return true;
    }

    public function deleteWidget(Widget $widget)
    {
        if (!$widget->delete()) {
            throw new \Exception('Error deleting app widget');
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

    public function getWidgetRepository(): WidgetRepository
    {
        return $this->widgetRepository;
    }
}
