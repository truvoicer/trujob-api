<?php

namespace Database\Seeders\admin;

use App\Models\Widget;
use Illuminate\Database\Seeder;

class WidgetSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = include_once(database_path('data/WidgetData.php'));
        if (!$data) {
            throw new \Exception('Error reading WidgetData.php file ' . database_path('data/WidgetData.php'));
        }
        foreach ($data as $item) {
            $name = $item['name'];
            unset($item['name']);
            $atts = $item;
            if (!empty($item['properties'])) {
                $atts['properties'] = json_encode($item['properties']);
            }
            $create = Widget::query()->updateOrCreate(
                ['name' => $name],
                $atts
            );
        }
    }
}
