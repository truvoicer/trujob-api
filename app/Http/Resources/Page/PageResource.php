<?php

namespace App\Http\Resources\Page;

use App\Helpers\SiteHelper;
use App\Http\Resources\RoleResource;
use App\Http\Resources\Sidebar\SidebarResource;
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

        [$site, $user] = SiteHelper::getCurrentSite();
        
        return [
            'id' => $this->id,
            'view' => $this->view,
            'permalink' => $this->permalink,
            'name' => $this->name,
            'title' => $this->title,
            'content' => $this->content,
            'blocks' => $this->whenLoaded('pageBlocks', PageBlockResource::collection($this->pageBlocks)),
            'has_sidebar' => $this->has_sidebar,
            'sidebars' => $this->whenLoaded('sidebars', SidebarResource::collection($this->sidebars)),
            'is_active' => $this->is_active,
            'is_home' => $this->is_home,
            'is_featured' => $this->is_featured,
            'is_protected' => $this->is_protected,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
            'has_permission' => $this->whenLoaded('roles', function () use($site, $user) {
                return $this->hasPermission($site, $this->roles, $user);
            }),
        ];
    }
}
