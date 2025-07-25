<?php

namespace Database\Factories;

use App\Enums\Product\Category\Pet\PetProperty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PetProduct>
 */
class PetProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            PetProperty::TYPE->value => $this->faker->word,
            PetProperty::BREED->value => $this->faker->word,
            PetProperty::AGE->value => $this->faker->word,
            PetProperty::COLOR->value => $this->faker->word,
            PetProperty::SIZE->value => $this->faker->word,
            PetProperty::WEIGHT->value => $this->faker->word,
            PetProperty::HEALTH_STATUS->value => $this->faker->word,
            PetProperty::VACCINATION_STATUS->value => $this->faker->word,
            PetProperty::MICROCHIP_STATUS->value => $this->faker->word,
            PetProperty::SPAYED_OR_NEUTERED->value => $this->faker->word,
            PetProperty::TRAINING_STATUS->value => $this->faker->word,
            PetProperty::TEMPERAMENT->value => $this->faker->word,
            PetProperty::SPECIAL_NEEDS->value => $this->faker->text,
            PetProperty::ADOPTION_FEE->value => $this->faker->word,
            PetProperty::AVAILABILITY->value => $this->faker->word,
            PetProperty::IS_RESCUE->value => $this->faker->boolean,
            PetProperty::IS_FOSTER->value => $this->faker->boolean,
            PetProperty::IS_ADOPTED->value => $this->faker->boolean,
            PetProperty::IS_AVAILABLE_FOR_ADOPTION->value => $this->faker->boolean,
            PetProperty::IS_AVAILABLE_FOR_FOSTER->value => $this->faker->boolean,
            PetProperty::IS_AVAILABLE_FOR_RESERVE->value => $this->faker->boolean,
            PetProperty::IS_AVAILABLE_FOR_SALE->value => $this->faker->boolean,
            PetProperty::IS_AVAILABLE_FOR_BREEDING->value => $this->faker->boolean,
            PetProperty::IS_AVAILABLE_FOR_STUD->value => $this->faker->boolean,
            PetProperty::IS_AVAILABLE_FOR_WALKING->value => $this->faker->boolean,
            PetProperty::IS_AVAILABLE_FOR_BOARDING->value => $this->faker->boolean,
            PetProperty::IS_AVAILABLE_FOR_PET_SITTING->value => $this->faker->boolean,
            PetProperty::IS_AVAILABLE_FOR_PET_TRAINING->value => $this->faker->boolean,
        ];
    }
}
