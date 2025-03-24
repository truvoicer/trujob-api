<?php

namespace App\Http\Resources\Menu;

use App\Http\Resources\PageResource;
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
        return [
            'id' => $this->id,
            'active' => $this->active,
            'title' => $this->title,
            'type' => $this->type,
            'url' => $this->url,
            'target' => $this->target,
            'order' => $this->order,
            'icon' => $this->icon,
            'li_class' => $this->li_class,
            'a_class' => $this->a_class,
            'menus' => $this->whenLoaded('menus', MenuResource::collection($this->menus)),
        ];
    }
}
