<?php
namespace App\Enums\Product;

enum ProductCategory: string
{
    case EVENT = 'event';
    case VEHICLE = 'vehicle';
    case SERVICE = 'service';
    case REAL_ESTATE = 'real-estate';
    case JOB = 'job';
    case PET = 'pet';
    case ITEM = 'item';
    case PROPERTY = 'property';
    case BUSINESS = 'business';
    case TICKET = 'ticket';
    case COURSE = 'course';
    case FOOD = 'food';

    public function label(): string
    {
        return match ($this) {
            self::EVENT => 'Event',
            self::VEHICLE => 'Vehicle',
            self::SERVICE => 'Service',
            self::REAL_ESTATE => 'Real Estate',
            self::JOB => 'Job',
            self::PET => 'Pet',
            self::ITEM => 'Item',
            self::PROPERTY => 'Property',
            self::BUSINESS => 'Business',
            self::TICKET => 'Ticket',
            self::COURSE => 'Course',
            self::FOOD => 'Food',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::EVENT => 'Event description',
            self::VEHICLE => 'Vehicle description',
            self::SERVICE => 'Service description',
            self::REAL_ESTATE => 'Real Estate description',
            self::JOB => 'Job description',
            self::PET => 'Pet description',
            self::ITEM => 'Item description',
            self::PROPERTY => 'Property description',
            self::BUSINESS => 'Business description',
            self::TICKET => 'Ticket description',
            self::COURSE => 'Course description',
            self::FOOD => 'Food description',
        };
    }
}
