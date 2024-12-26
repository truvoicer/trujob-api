<?php

namespace Database\Factories\messaging;

use App\Models\Listing;
use App\Models\MessagingGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MessagingGroup>
 */
class MessagingGroupFactory extends Factory
{
    protected $model = MessagingGroup::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (MessagingGroup $messagingGroup) {
            $listing = $messagingGroup->listing()->first();
            if ($listing->id === $messagingGroup->listing_id) {
                $newListing = Listing::where('id', '<>', $messagingGroup->listing_id)->first();
                $messagingGroup->listing_id = $newListing->id;
            }
            return $messagingGroup;
        });
    }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $listingIds = array_map(function ($user) {
            return $user['id'];
        }, Listing::all()->toArray());

        return [
            'listing_id' => fake()->randomElement($listingIds)
        ];
    }
}
