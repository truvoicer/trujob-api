<?php

namespace Database\Seeders\admin;

use App\Models\Block;
use App\Models\Page;
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
            $blocks = [];
            if (!empty($item['blocks']) && is_array($item['blocks'])) {
                $blocks = $item['blocks'];
            }
            if (!empty($item['blocks'])) {
                unset($item['blocks']);
            }
            $create = Page::query()->updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
            foreach ($blocks as $block) {
                $pageService->createPageBlock($create, $block);
            }
        }
    }
}
