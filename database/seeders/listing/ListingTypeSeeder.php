<?php

namespace Database\Seeders\listing;

use App\Models\ListingType;
use Illuminate\Database\Seeder;

class ListingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = include_once(database_path('data/ListingTypeData.php'));
        if (!$data) {
            throw new \Exception('Error reading ListingTypeData.php file ' . database_path('data/ListingTypeData.php'));
        }
        foreach ($data as $index => $item) {
            if (empty($item['slug'])) {
                throw new \Exception(
                    sprintf(
                        'Error at %d, slug is required | ListingTypeData.php',
                        $index
                    )
                );
            }
            if (empty($item['title'])) {
                $item['title'] = ucfirst($item['slug']);
            }
            $create = ListingType::query()->updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
