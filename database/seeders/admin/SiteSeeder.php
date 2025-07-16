<?php

namespace Database\Seeders\admin;

use App\Enums\Media\FileSystemType;
use App\Enums\Media\MediaType;
use App\Enums\Media\Types\Image\ImageCategory;
use App\Enums\Price\PriceType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Feature;
use App\Models\Media;
use App\Models\MessagingGroup;
use App\Models\MessagingGroupMessage;
use App\Models\Price;
use App\Models\Site;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\UserMedia;
use App\Models\UserProfile;
use App\Models\UserReview;
use App\Models\UserReward;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductFollow;
use App\Models\ProductReview;
use App\Services\Data\DefaultData;
use Exception;
use Illuminate\Database\Eloquent\Factories\Sequence;

class SiteSeeder extends Seeder
{

    public function priceState(): array
    {
        return array_map(function (PriceType $priceType) {
            return [
                'price_type' => $priceType->value,
            ];
        }, PriceType::cases());
    }

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
            $settings = $item['settings'] ?? null;
            unset($item['settings']);

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
                        ->sequence(
                            function (Sequence $sequence) {
                                if ($sequence->index === 0) {
                                    return DefaultData::TEST_USER_DATA;
                                }
                                return [];
                            }
                        )
                        ->has(
                            Product::factory()
                                ->count(5)
                                ->has(ProductReview::factory()->count(5))
                                ->has(ProductFollow::factory()->count(5))
                                ->has(
                                    Media::factory()
                                        ->count(1)
                                        ->state(function (array $attributes, Product $product) {
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
                                ->has(
                                    Price::factory()
                                        ->sequence(...$this->priceState())
                                        ->count(count(PriceType::cases()))
                                )
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
            if (is_array($settings) && count($settings) > 0) {
                Site::first()->settings()->create($settings);
            }
        }
        foreach (User::all() as $user) {
            if ($user->products->count() == 0) {
                continue;
            }
            foreach ($user->products as $product) {
                $product->categories()->attach(
                    Category::all()->random(1)
                        ->pluck('id')
                );
                $product->brands()->attach(
                    Brand::all()->random(1)
                        ->pluck('id')
                );
                $product->colors()->attach(
                    Color::all()->random(1)
                        ->pluck('id')
                );
                $product->features()->attach(
                    Feature::all()->random(1)
                        ->pluck('id')
                );
                $product->productCategories()->attach(
                    ProductCategory::all()->random(1)
                        ->pluck('id')
                );
                $product->refresh();
                $product->update([
                    'sku' => $product->generateSku(),
                ]);
            }
        }
    }
}
