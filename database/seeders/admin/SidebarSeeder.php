<?php

namespace Database\Seeders\admin;

use App\Models\Sidebar;
use App\Models\Site;
use App\Services\Admin\Sidebar\SidebarService;
use Illuminate\Database\Seeder;

class SidebarSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(SidebarService $sidebarService): void
    {
        $data = include_once(database_path('data/SidebarData.php'));
        if (!$data) {
            throw new \Exception('Error reading SidebarData.php file ' . database_path('data/SidebarData.php'));
        }
        foreach ($data as $index => $sidebar) {
            $site = Site::where('name', $sidebar['site'])->first();
            if (!$site) {
                throw new \Exception('Site not found: ' . $sidebar['site']);
            }
            unset($sidebar['site']);
            $sidebarService->setSite($site);
            $sidebar['site_id'] = $site->id;
            $getSidebar = Sidebar::where('name', $sidebar['name'])->where('site_id', $site->id)->first();
            if (!$getSidebar) {
                if (!$sidebarService->createSidebar($sidebar)) {
                    throw new \Exception('Error creating sidebar: ' . $index);
                }
            } else {
                if (!$sidebarService->updateSidebar($getSidebar, $sidebar)) {
                    throw new \Exception('Error updating sidebar: ' . $index);
                }
            }
        }
    }
}
