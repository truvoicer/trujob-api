<?php

namespace App\Enums\Product\Type\Pet;

enum PetProperty: string
{
    case TYPE = 'type';
    case BREED = 'breed';
    case AGE = 'age';
    case COLOR = 'color';
    case SIZE = 'size';
    case WEIGHT = 'weight';
    case HEALTH_STATUS = 'health_status';
    case VACCINATION_STATUS = 'vaccination_status';
    case MICROCHIP_STATUS = 'microchip_status';
    case SPAYED_OR_NEUTERED = 'spayed_or_neutered';
    case TRAINING_STATUS = 'training_status';
    case TEMPERAMENT = 'temperament';
    case SPECIAL_NEEDS = 'special_needs';
    case ADOPTION_FEE = 'adoption_fee';
    case AVAILABILITY = 'availability';
    case IS_RESCUE = 'is_rescue';
    case IS_FOSTER = 'is_foster';
    case IS_ADOPTED = 'is_adopted';
    case IS_AVAILABLE_FOR_ADOPTION = 'is_available_for_adoption';
    case IS_AVAILABLE_FOR_FOSTER = 'is_available_for_foster';
    case IS_AVAILABLE_FOR_RESERVE = 'is_available_for_reserve';
    case IS_AVAILABLE_FOR_SALE = 'is_available_for_sale';
    case IS_AVAILABLE_FOR_BREEDING = 'is_available_for_breeding';
    case IS_AVAILABLE_FOR_STUD = 'is_available_for_stud';
    case IS_AVAILABLE_FOR_WALKING = 'is_available_for_walking';
    case IS_AVAILABLE_FOR_BOARDING = 'is_available_for_boarding';
    case IS_AVAILABLE_FOR_PET_SITTING = 'is_available_for_pet_sitting';
    case IS_AVAILABLE_FOR_PET_TRAINING = 'is_available_for_pet_training';
}