<?php

namespace App\Http\Resources\User;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
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
            'settings' => $this->whenLoaded('userSetting', UserSettingResource::make($this->userSetting)),
            'profile' => $this->whenLoaded('userProfile', UserProfileResource::make($this->userProfile)),
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
