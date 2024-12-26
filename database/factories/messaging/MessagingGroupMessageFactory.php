<?php

namespace Database\Factories\messaging;

use App\Models\MessagingGroupMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MessagingGroupMessage>
 */
class MessagingGroupMessageFactory extends Factory
{
    protected $model = MessagingGroupMessage::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'message' => fake()->text(50)
        ];
    }
}
