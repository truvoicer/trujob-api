<?php

namespace App\Http\Resources\Sidebar;

use App\Http\Resources\RoleResource;
use App\Http\Resources\Widget\WidgetResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SidebarWidgetResource extends JsonResource
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
            'title' => $this->title,
            'icon' => $this->icon,
            'has_container' => $this->has_container,
            'properties' => $this->properties,
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
        ];
    }
}
