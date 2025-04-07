<?php

namespace App\Http\Resources\Page;

use App\Http\Resources\RoleResource;
use App\Http\Resources\Sidebar\SidebarResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageBlockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->block->type,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'background_image' => $this->background_image,
            'background_color' => $this->background_color,
            'pagination_type' => $this->pagination_type,
            'pagination' => (bool)$this->pagination,
            'pagination_scroll_type' => $this->pagination_scroll_type,
            'content' => $this->content,
            'properties' => $this->properties,
            'order' => $this->order,
            'has_sidebar' => (bool)$this->has_sidebar,
            'sidebars' => $this->whenLoaded('sidebars', SidebarResource::collection($this->sidebars)),
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
        ];
    }
}
