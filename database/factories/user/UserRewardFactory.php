<?php

namespace Database\Factories\user;

use App\Models\UserReward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserReward>
 */
class UserRewardFactory extends Factory
{
    protected $model = UserReward::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $data = [
            ['icon' => 'fa-trophy', 'label' => 'Top Seller'],
            ['icon' => 'fa-trophy', 'label' => 'Top Buyer'],
            ['icon' => 'fa-soccer', 'label' => 'Fast Delivery'],
            ['icon' => 'fa-briefcase', 'label' => 'Lightning Speed Delivery'],
            ['icon' => 'fa-star', 'label' => 'Top Rated Customer'],
        ];
        $dataKey = fake()->randomKey($data);
        return [
            'icon' => $data[$dataKey]['icon'],
            'label' => $data[$dataKey]['label'],
        ];
    }
}
