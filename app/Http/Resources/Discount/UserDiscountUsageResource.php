<?php

namespace App\Http\Resources\Discount;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDiscountUsageResource extends JsonResource
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
            'message' => 'Discount usage tracked successfully',
            'remaining_uses' => $this->discount->usage_limit ? $this->discount->usage_limit - $this->discount->usage_count : null,
            'user_remaining_uses' => $this->discount->per_user_limit ? $this->discount->per_user_limit - $usage->usage_count : null,
        ];
    }
}
