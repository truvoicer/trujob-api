<?php

namespace Database\Factories\user;

use App\Models\UserMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserMedia>
 */
class UserMediaFactory extends Factory
{
    protected $model = UserMedia::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => 'image',
            'filesystem' => 'external_link',
            'category' => 'avatar',
            'alt' => fake()->text(20),
            'url' => fake()->imageUrl(640, 480, 'Avatar'),
        ];
    }
}
