<?php

namespace Database\Factories;

use App\Enums\Product\Category\Ticket\TicketProperty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            TicketProperty::WEBSITE => $this->faker->url,
            TicketProperty::PHONE => $this->faker->phoneNumber,
            TicketProperty::EMAIL => $this->faker->email,
            TicketProperty::OPENING_HOURS => $this->faker->time,
            TicketProperty::CLOSING_HOURS => $this->faker->time,
            TicketProperty::TYPE => $this->faker->word,
            TicketProperty::START_DATE => $this->faker->dateTimeBetween('now', '+1 year'),
            TicketProperty::END_DATE => $this->faker->dateTimeBetween('now', '+1 year'),
            TicketProperty::START_TIME => $this->faker->time,
            TicketProperty::END_TIME => $this->faker->time,
            TicketProperty::SEAT_NUMBER => $this->faker->randomNumber(),
            TicketProperty::SEAT_ROW => $this->faker->word,
            TicketProperty::SEAT_SECTION => $this->faker->word,
            TicketProperty::SEAT => $this->faker->word,
            TicketProperty::SEAT_SIDE => $this->faker->word,
            TicketProperty::SEAT_LEVEL => $this->faker->word,
            TicketProperty::SEAT_GATE => $this->faker->word,
            TicketProperty::SEAT_AREA => $this->faker->word,
        ];
    }
}
