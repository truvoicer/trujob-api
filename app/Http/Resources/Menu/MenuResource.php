<?php

namespace App\Http\Resources\Menu;

use App\Helpers\SiteHelper;
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
        // if ($this->name === 'header-account-menu') {
        //     dd($this->menuItems);
        // }
        [$site, $user] = SiteHelper::getCurrentSite();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'has_parent' => $this->hasParent(),
            'ul_class' => $this->ul_class,
            'active' => $this->active,
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
            'menu_items' => $this->whenLoaded('menuItems', function () {
                return MenuItemResource::collection($this->menuItems);
            }),
            'has_permission' => $this->whenLoaded('roles', function () use($site, $user) {
                return $this->hasPermission($site, $this->roles, $user);
            }),
        ];
    }
}
