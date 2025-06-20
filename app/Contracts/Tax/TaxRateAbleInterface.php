<?php

namespace App\Contracts\Tax;

use App\Models\TaxRate;
use Illuminate\Http\Resources\Json\JsonResource;

interface TaxRateAbleInterface
{
    public function validateRequest(): bool;
    public function attachTaxRateAble(TaxRate $taxRate, array $data): void;
    public function detachTaxRateAble(TaxRate $taxRate, array $data): void;
    public function getTaxRateableEntityResourceData(JsonResource $resource): array;

}
