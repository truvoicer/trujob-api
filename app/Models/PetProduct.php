<?php

namespace App\Models;

use App\Enums\Product\Category\Pet\PetProperty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_user_id',
        'product_id',
        PetProperty::TYPE->value,
        PetProperty::BREED->value,
        PetProperty::AGE->value,
        PetProperty::COLOR->value,
        PetProperty::SIZE->value,
        PetProperty::WEIGHT->value,
        PetProperty::HEALTH_STATUS->value,
        PetProperty::VACCINATION_STATUS->value,
        PetProperty::MICROCHIP_STATUS->value,
        PetProperty::SPAYED_OR_NEUTERED->value,
        PetProperty::TRAINING_STATUS->value,
        PetProperty::TEMPERAMENT->value,
        PetProperty::SPECIAL_NEEDS->value,
        PetProperty::ADOPTION_FEE->value,
        PetProperty::AVAILABILITY->value,
        PetProperty::IS_RESCUE->value,
        PetProperty::IS_FOSTER->value,
        PetProperty::IS_ADOPTED->value,
        PetProperty::IS_AVAILABLE_FOR_ADOPTION->value,
        PetProperty::IS_AVAILABLE_FOR_FOSTER->value,
        PetProperty::IS_AVAILABLE_FOR_RESERVE->value,
        PetProperty::IS_AVAILABLE_FOR_SALE->value,
        PetProperty::IS_AVAILABLE_FOR_BREEDING->value,
        PetProperty::IS_AVAILABLE_FOR_STUD->value,
        PetProperty::IS_AVAILABLE_FOR_WALKING->value,
        PetProperty::IS_AVAILABLE_FOR_BOARDING->value,
        PetProperty::IS_AVAILABLE_FOR_PET_SITTING->value,
        PetProperty::IS_AVAILABLE_FOR_PET_TRAINING->value,
    ];
}
