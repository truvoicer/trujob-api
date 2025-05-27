<?php

namespace App\Http\Resources\Shipping;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingRestrictionResource extends JsonResource
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
            'type' => $this->type, // Assuming type is an enum or string
            'restriction_id' => $this->restriction_id, // Assuming restriction_id is an integer
            'action' => $this->action, // Assuming type is an enum or string
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
