<?php
namespace App\Enums\Order\Tax;

enum TaxRateType: string
{
    case VAT = 'vat';
    case DUTY = 'duty';
    case SERVICE = 'service';
    case EXCISE = 'excise';
    case SALES_TAX = 'sales_tax';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::VAT => __('Value Added Tax'),
            self::DUTY => __('Customs Duty'),
            self::SERVICE => __('Service Tax'),
            self::EXCISE => __('Excise Duty'),
            self::SALES_TAX => __('Sales Tax'),
            self::OTHER => __('Other Tax'),
        };
    }
}