<?php

namespace App\Http\Resources\AppMenu;

use Illuminate\Http\Resources\Json\JsonResource;

class AppMenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['appMenuItems'] = AppMenuItemResource::collection($this->appMenuItem);
        return $data;
    }
}
