<?php

namespace App\Http\Resources\Menu;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'view' => $this->view,
            'slug' => $this->slug,
            'title' => $this->title,
            'is_active' => $this->is_active,
            'is_home' => $this->is_home,
            'is_featured' => $this->is_featured,
            'is_protected' => $this->is_protected,
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
        ];
    }
}
