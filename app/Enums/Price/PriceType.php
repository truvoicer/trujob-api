<?php
namespace App\Enums\Price;

enum PriceType: string
{
    case SUBSCRIPTION = 'subscription';
    case ONE_TIME = 'one_time';

    public function label(): string
    {
        return match ($this) {
            self::SUBSCRIPTION => 'Subscription',
            self::ONE_TIME => 'One Time',
        };
    }

    public static function buildList(): array
    {
        return array_map(
            fn(self $type) => $type->listItem(),
            self::cases()
        );
    }

    public function listItem(): array
    {
        return [
            'name' => $this->value,
            'label' => $this->label(),
        ];
    }
}
