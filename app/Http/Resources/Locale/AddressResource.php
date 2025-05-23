<?php

namespace App\Http\Resources\Locale;

use App\Http\Resources\Listing\CountryResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'label' => $this->label,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'type' => $this->type,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'user' => $this->whenLoaded('user', UserResource::make($this->user)),
            'country' => $this->whenLoaded('country', CountryResource::make($this->country)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
