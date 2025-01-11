<?php

namespace Database\Seeders\admin;

use App\Models\Page;
use App\Models\View;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = include_once(database_path('data/PageData.php'));
        if (!$data) {
            throw new \Exception('Error reading PageData.php file ' . database_path('data/PageData.php'));
        }
        foreach ($data as $item) {
            $create = Page::query()->updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
            $create->blocks()->createMany($item['blocks']);
        }
    }
}
