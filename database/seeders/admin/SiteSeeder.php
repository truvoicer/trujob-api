<?php

namespace Database\Seeders\admin;

use App\Enums\Media\FileSystemType;
use App\Enums\Media\MediaType;
use App\Enums\Media\Types\Image\ImageCategory;
use App\Models\Media;
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
            Site::factory()
            ->count(1)
            ->has(
                Media::factory()
                ->count(1)
                ->state(function (array $attributes, Site $site) {
                    $randomNumberBetween = random_int(1, 100);
                    return[
                        'type' => MediaType::IMAGE,
                        'filesystem' => FileSystemType::EXTERNAL,
                        'category' => ImageCategory::LOGO,
                        'alt' => fake()->text(20),
                        'url' => "https://picsum.photos/id/{$randomNumberBetween}/640/480",
                    ];
                })
            )
            ->create($item);
        }
    }
}
