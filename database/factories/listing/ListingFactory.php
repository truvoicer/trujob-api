<?php

namespace Database\Factories\listing;

use App\Models\Listing;
use App\Services\HelperService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    protected $model = Listing::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $fake = fake();
        $title = $fake->text(20);
        return [
            "slug" => HelperService::toSlug($title),
            "title" => $title,
            "description" => $fake->text(100),
            "active" => $fake->boolean(),
            "allow_offers" => $fake->boolean(),
        ];
    }
}
