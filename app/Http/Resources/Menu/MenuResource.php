<?php

namespace App\Http\Resources\Menu;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'has_parent' => $this->hasParent(),
            'ul_class' => $this->ul_class,
            'active' => $this->active,
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
            'menu_items' => $this->whenLoaded('menuItems', MenuItemResource::collection($this->menuItems))
        ];
    }
}
