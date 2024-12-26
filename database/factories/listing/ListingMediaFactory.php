<?php

namespace Database\Factories\listing;

use App\Models\ListingMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingMedia>
 */
class ListingMediaFactory extends Factory
{
    private string $loremPicsumUrl = 'https://picsum.photos/id';

    protected $model = ListingMedia::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (ListingMedia $listingMedia) {
            $imageUrl = match ($listingMedia->category) {
                'listing_image' => "{$this->loremPicsumUrl}/{$listingMedia->id}/700/700",
                default =>  "{$this->loremPicsumUrl}/{$listingMedia->id}/300/300",
            };
            $listingMedia->url = $imageUrl;
            $listingMedia->save();
            return $listingMedia;
        });
    }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $category = fake()->randomElement(['listing_image', 'thumbnail']);
        return [
            'type' => 'image',
            'filesystem' => 'external_link',
            'category' => $category,
            'alt' => fake()->text(20),
        ];
    }
}
