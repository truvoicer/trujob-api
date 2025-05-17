<?php

namespace App\Jobs;

use App\Services\Admin\Menu\MenuService;
use App\Services\Block\BlockService;
use App\Services\Page\PageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DefaultSiteData implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PageService $pageService, MenuService $menuService, BlockService $blockService): void
    {
        $blockService->defaultBlockTypes();
        $pageService->defaultPages();
        $menuService->defaultMenus();
    }
}
