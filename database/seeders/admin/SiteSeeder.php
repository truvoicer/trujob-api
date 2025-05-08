<?php

namespace Database\Seeders\admin;

use App\Enums\Media\FileSystemType;
use App\Enums\Media\MediaType;
use App\Enums\Media\Types\Image\ImageCategory;
use App\Models\Media;
use App\Models\MessagingGroup;
use App\Models\MessagingGroupMessage;
use App\Models\Site;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\UserMedia;
use App\Models\UserProfile;
use App\Models\UserReview;
use App\Models\UserReward;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;
use App\Models\Listing;
use App\Models\ListingBrand;
use App\Models\ListingCategory;
use App\Models\ListingColor;
use App\Models\ListingFeature;
use App\Models\ListingFollow;
use App\Models\ListingProductType;
use App\Models\ListingReview;

class SiteSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $siteData = include(database_path('data/SiteData.php'));
        if (!$siteData) {
            throw new \Exception('Error reading SiteData.php file ' . database_path('data/SiteData.php'));
        }

        foreach ($siteData as $item) {
            Site::factory()
                ->count(1)
                ->has(
                    Media::factory()
                        ->count(1)
                        ->state(function (array $attributes, Site $site) {
                            $randomNumberBetween = random_int(1, 100);
                            return [
                                'type' => MediaType::IMAGE,
                                'filesystem' => FileSystemType::EXTERNAL,
                                'category' => ImageCategory::LOGO,
                                'alt' => fake()->text(20),
                                'url' => "https://picsum.photos/id/{$randomNumberBetween}/640/480",
                            ];
                        })
                )
                ->has(
                    User::factory()
                        ->count(10)
                        ->has(
                            Listing::factory()
                                ->count(5)
                                ->has(ListingFeature::factory()->count(1))
                                ->has(ListingFeature::factory()->count(1))
                                ->has(ListingReview::factory()->count(5))
                                ->has(ListingFollow::factory()->count(5))
                                ->has(ListingBrand::factory()->count(1))
                                ->has(ListingColor::factory()->count(1))
                                ->has(
                                    Media::factory()
                                        ->count(1)
                                        ->state(function (array $attributes, Listing $listing) {
                                            $randomNumberBetween = random_int(1, 100);
                                            return [
                                                'type' => MediaType::IMAGE,
                                                'filesystem' => FileSystemType::EXTERNAL,
                                                'category' => ImageCategory::THUMBNAIL,
                                                'alt' => fake()->text(20),
                                                'url' => "https://picsum.photos/id/{$randomNumberBetween}/700/700",
                                            ];
                                        })
                                )
                                ->has(Media::factory()->count(5))
                                ->has(ListingCategory::factory()->count(5))
                                ->has(ListingProductType::factory()->count(5))
                        )
                        ->has(UserFollow::factory()->count(5))
                        ->has(UserProfile::factory()->count(1))
                        ->has(UserReview::factory()->count(5))
                        ->has(UserReward::factory()->count(5))
                        ->has(UserSetting::factory()->count(1))
                        ->has(UserMedia::factory()->count(1))
                        ->has(
                            MessagingGroup::factory()
                                ->has(MessagingGroupMessage::factory()->count(5))
                                ->count(5)
                        )
                )
                ->create($item);
        }
    }
}
