<?php

namespace Database\Seeders\admin;

use App\Models\Menu;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Site;
use App\Services\Admin\Menu\MenuService;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(MenuService $menuService): void
    {
        $data = include_once(database_path('data/MenuData.php'));
        if (!$data) {
            throw new \Exception('Error reading MenuData.php file ' . database_path('data/MenuData.php'));
        }
        foreach ($data as $index => $menu) {
            $site = Site::where('name', $menu['site'])->first();
            if (!$site) {
                throw new \Exception('Site not found: ' . $menu['site']);
            }
            unset($menu['site']);
            $menu['site_id'] = $site->id;
            $getMenu = Menu::where('name', $menu['name'])->where('site_id', $site->id)->first();
            if (!$getMenu) {
                if (!$menuService->createMenu($menu)) {
                    throw new \Exception('Error creating menu: ' . $index);
                }
            } else {
                $menuService->setMenu($getMenu);
                if (!$menuService->updateMenu($menu)) {
                    throw new \Exception('Error updating menu: ' . $index);
                }
            }
        }
    }
}
