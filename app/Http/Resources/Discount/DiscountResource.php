<?php

namespace App\Http\Resources\Discount;

use App\Http\Resources\Product\CurrencyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'amount' => $this->amount,
            'rate' => $this->rate,
            'currency_id' => $this->whenLoaded('currency', CurrencyResource::make($this->currency)),
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'is_active' => $this->is_active,
            'usage_limit' => $this->usage_limit,
            'per_user_limit' => $this->per_user_limit,
            'min_order_amount' => $this->min_order_amount,
            'min_items_quantity' => $this->min_items_quantity,
            'scope' => $this->scope,
            'code' => $this->code,
            'is_code_required' => $this->is_code_required,
            // 'products' => $this->whenLoaded('products', ),
            // 'category_ids' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
