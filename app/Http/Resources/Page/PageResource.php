<?php

namespace App\Http\Resources\Page;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
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
            'content' => $this->content,
            'blocks' => $this->whenLoaded('pageBlocks', PageBlockResource::collection($this->pageBlocks)),
            'has_sidebar' => $this->has_sidebar,
            'sidebar_widgets' => $this->sidebar_widgets,
            'is_active' => $this->is_active,
            'is_home' => $this->is_home,
            'is_featured' => $this->is_featured,
            'is_protected' => $this->is_protected,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
        ];
    }
}
