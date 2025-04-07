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
            'title' => $this->pivot->title,
            'icon' => $this->pivot->icon,
            'has_container' => $this->pivot->has_container,
            'order' => $this->pivot->order,
            'properties' => json_decode($this->pivot->properties),
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
        ];
    }
}
