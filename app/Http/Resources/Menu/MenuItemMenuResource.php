<?php

namespace App\Http\Resources\Menu;

use App\Helpers\SiteHelper;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemMenuResource extends JsonResource
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
            'order' => $this->order,
            'menu' => $this->whenLoaded('menu', function () {
                return new MenuResource($this->menu);
            }),
            'menu_item' => $this->whenLoaded('menuItem', function () {
                return new MenuItemResource($this->menuItem);
            }),
        ];
    }
}
