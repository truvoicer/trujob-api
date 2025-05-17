<?php

namespace App\Console\Commands;

use App\Jobs\DefaultSiteData;
use Illuminate\Console\Command;

class DefaultPageMenus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:default-page-menus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set default pages and menus';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DefaultSiteData::dispatch();
    }
}
