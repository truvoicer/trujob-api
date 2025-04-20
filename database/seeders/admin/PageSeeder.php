<?php

namespace Database\Seeders\admin;

use App\Models\Block;
use App\Models\Page;
use App\Models\Site;
use App\Services\Page\PageService;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(PageService $pageService): void
    {
        $data = include_once(database_path('data/PageData.php'));
        if (!$data) {
            throw new \Exception('Error reading PageData.php file ' . database_path('data/PageData.php'));
        }
        foreach ($data as $item) {
            $site = Site::find($item['site_id'])->first();
            if (!$site) {
                throw new \Exception('Site not found: ' . $item['site_id']);
            }
            $findPage = $site->pages()->where('name', $item['name'])->first();
            if ($findPage) {
                if (!$pageService->updatePage($findPage, $item)) {
                    throw new \Exception('Error updating page: ' . $item['name']);
                }
                continue;
            }
            $pageService->createPage($site, $item);
        }
    }
}
