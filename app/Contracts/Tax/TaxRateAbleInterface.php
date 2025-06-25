<?php

namespace App\Contracts\Tax;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TaxRate;
use App\Models\TaxRateAble;
use Illuminate\Http\Resources\Json\JsonResource;

interface TaxRateAbleInterface
{
    public function validateRequest(): bool;
    public function attachTaxRateAble(TaxRate $taxRate, array $data): void;
    public function detachTaxRateAble(TaxRate $taxRate, array $data): void;
    public function getTaxRateableEntityResourceData(JsonResource $resource): array;
    public function isTaxRateValidForOrderItem(TaxRateAble $taxRateAble, OrderItem $orderItem): bool;
    public function isTaxRateValidForOrder(TaxRateAble $taxRateAble, Order $order): bool;
}
