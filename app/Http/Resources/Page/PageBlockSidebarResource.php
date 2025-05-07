<?php

namespace App\Http\Resources\Page;

use App\Http\Resources\Sidebar\SidebarResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageBlockSidebarResource extends JsonResource
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
            'page_block' => $this->whenLoaded('pageBlock', function () {
                return new PageBlockResource($this->pageBlock);
            }),
            'sidebar' => $this->whenLoaded('sidebar', function () {
                return new SidebarResource($this->sidebar);
            }),
            'order' => $this->order,
            'active' => $this->active,
        ];
    }
}
