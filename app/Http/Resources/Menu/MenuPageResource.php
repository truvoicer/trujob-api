<?php

namespace App\Http\Resources\Menu;

use App\Helpers\SiteHelper;
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
        [$site, $user] = SiteHelper::getCurrentSite();

        return [
            'id' => $this->id,
            'view' => $this->view,
            'name' => $this->name,
            'label' => $this->label,
            'is_active' => $this->is_active,
            'is_home' => $this->is_home,
            'is_featured' => $this->is_featured,
            'is_protected' => $this->is_protected,
            'roles' => $this->whenLoaded('roles', RoleResource::collection($this->roles)),
            'has_permission' => $this->whenLoaded('roles', function () use($site, $user) {
                return $this->hasPermission($site, $this->roles, $user);
            }),
        ];
    }
}
