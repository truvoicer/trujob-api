<?php

namespace App\Console\Commands;

use App\Jobs\DefaultSiteData;
use App\Services\Admin\Menu\MenuService;
use App\Services\Block\BlockService;
use App\Services\Page\PageService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class DefaultPageMenus extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:default-page-menus {--queue : Whether the job should be queued}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set default pages and menus';

    /**
     * Execute the console command.
     */
    public function handle(
        PageService $pageService,
        MenuService $menuService,
        BlockService $blockService
    )
    {

        $queue = $this->option('queue');
        if ($queue) {
            $this->info('Dispatching job to queue...');
            DefaultSiteData::dispatch();
        } else {
            $this->info('Executing job immediately...');
            $blockService->defaultBlockTypes();
            $pageService->defaultPages();
            $menuService->defaultMenus();
        }
    }
}
