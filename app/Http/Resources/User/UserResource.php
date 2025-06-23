<?php

namespace App\Http\Resources\User;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'addresses' => $this->whenLoaded('addresses', $this->addresses),
            'settings' => $this->whenLoaded('settings', UserSettingResource::make($this->settings)),
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
