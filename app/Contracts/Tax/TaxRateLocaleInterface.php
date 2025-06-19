<?php

namespace App\Contracts\Tax;

use App\Models\TaxRate;
use Illuminate\Http\Resources\Json\JsonResource;

interface TaxRateLocaleInterface
{
    public function validateRequest(): bool;
    public function attachTaxRateLocale(TaxRate $taxRate, array $data): void;
    public function detachTaxRateLocale(TaxRate $taxRate, array $data): void;
    public function getLocaleableEntityResourceData(JsonResource $resource): array;

}
