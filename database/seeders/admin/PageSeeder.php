<?php

namespace Database\Seeders\admin;

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
        $pageService->defaultPages();
    }
}
