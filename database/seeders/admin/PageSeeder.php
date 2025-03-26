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
            $pageService->createPage($item);
        }
    }
}
