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
        $data = include(database_path('data/ListingTypeData.php'));
        if (!$data) {
            throw new \Exception('Error reading ListingTypeData.php file ' . database_path('data/ListingTypeData.php'));
        }
        foreach ($data as $index => $item) {
            if (empty($item['name'])) {
                throw new \Exception(
                    sprintf(
                        'Error at %d, name is required | ListingTypeData.php',
                        $index
                    )
                );
            }
            if (empty($item['label'])) {
                $item['label'] = ucfirst($item['name']);
            }
            $create = ListingType::query()->updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
