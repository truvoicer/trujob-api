<?php

namespace Database\Seeders\admin;

use App\Models\View;
use Illuminate\Database\Seeder;

class ViewSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = include_once(database_path('data/ViewData.php'));
        if (!$data) {
            throw new \Exception('Error reading ViewData.php file ' . database_path('data/ViewData.php'));
        }
        foreach ($data as $item) {
            $create = View::query()->updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
