<?php

namespace Database\Seeders\admin;

use App\Models\Site;
use App\Models\Widget;
use App\Services\Admin\Widget\WidgetService;
use Illuminate\Database\Seeder;

class WidgetSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(WidgetService $widgetService): void
    {
        $data = include(database_path('data/WidgetData.php'));
        if (!$data) {
            throw new \Exception('Error reading WidgetData.php file ' . database_path('data/WidgetData.php'));
        }
        foreach ($data as $item) {
            if (empty($item['name'])) {
                throw new \Exception('Error reading WidgetData.php file ' . database_path('data/WidgetData.php') . ' name is empty');
            }
            if (empty($item['site_id'])) {
                throw new \Exception('Error reading WidgetData.php file ' . database_path('data/WidgetData.php') . ' site_id is empty');
            }
            $site = Site::query()->find($item['site_id']);
            if (!$site) {
                throw new \Exception('Error reading WidgetData.php file ' . database_path('data/WidgetData.php') . ' site_id not found');
            }
            $widget = $widgetService->widgetFetch($site, $item['name']->value);
            if ($widget) {
                $widgetService->updateWidget($widget, $item);
                continue;
            }
            $widgetService->createWidget($site, $item);
        }
    }
}
