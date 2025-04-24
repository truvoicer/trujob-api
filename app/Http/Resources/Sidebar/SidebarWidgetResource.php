<?php

namespace App\Http\Resources\Sidebar;

use App\Helpers\SiteHelper;
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
        [$site, $user] = SiteHelper::getCurrentSite();
        return [
            'id' => $this->id,
            'name' => $this->whenLoaded('widget', $this->widget->name),
            'title' => $this->title,
            'icon' => $this->icon,
            'properties' => $this->properties,
            'order' => $this->order,
            'has_container' => $this->has_container,
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
            'has_permission' => $this->whenLoaded('roles', function () use($site, $user) {
                return $this->hasPermission($site, $this->roles, $user);
            }),
        ];
    }
}
