<?php

namespace Database\Seeders\admin;

use App\Services\Admin\Menu\MenuService;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(MenuService $menuService): void
    {
        $menuService->defaultMenus();
    }
}
