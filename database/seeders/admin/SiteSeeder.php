<?php

namespace Database\Seeders\admin;

use App\Models\Setting;
use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = include_once(database_path('data/SiteData.php'));
        if (!$data) {
            throw new \Exception('Error reading SiteData.php file ' . database_path('data/SiteData.php'));
        }
        foreach ($data as $item) {
            $create = Site::query()->updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
