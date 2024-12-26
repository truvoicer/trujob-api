<?php

namespace Database\Seeders\locale;

use App\Services\Locale\LocaleImportService;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Command\Command as CommandAlias;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(LocaleImportService $localeImportService)
    {
        $import = $localeImportService->runImport();
    }
}
