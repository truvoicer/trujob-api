<?php

namespace App\Http\Resources\PaymentGateway;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\PaymentGateway
 */
class PaymentGatewayResource extends JsonResource
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
            'label' => $this->label,
            'description' => $this->description,
            'icon' => $this->icon,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'settings' => $this->settings,
            'required_fields' => $this->required_fields,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
