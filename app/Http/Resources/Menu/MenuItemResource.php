<?php

namespace App\Http\Resources\Menu;

use App\Helpers\SiteHelper;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
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
            'active' => $this->active,
            'label' => $this->label,
            'type' => $this->type,
            'url' => $this->url,
            'target' => $this->target,
            'order' => $this->order,
            'icon' => $this->icon,
            'li_class' => $this->li_class,
            'a_class' => $this->a_class,
            'page' => $this->whenLoaded('page', new MenuPageResource($this->page)),
            'has_permission' => $this->hasPermission($site, $this->roles, $user),
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
            'menus' => $this->whenLoaded('menus', MenuResource::collection($this->menus)),
        ];
    }
}
