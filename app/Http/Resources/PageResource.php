<?php

namespace App\Http\Resources;

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
            'blocks' => $this->whenLoaded('blocks', PageBlockResource::collection($this->blocks)),
            'settings' => [
                'meta_title' => $this->settings['meta_title'] ?? '',
                'meta_description' => $this->settings['meta_description'] ?? '',
                'meta_keywords' => $this->settings['meta_keywords'] ?? '',
                'meta_robots' => $this->settings['meta_robots'] ?? '',
                'meta_canonical' => $this->settings['meta_canonical'] ?? '',
                'meta_author' => $this->settings['meta_author'] ?? '',
                'meta_publisher' => $this->settings['meta_publisher'] ?? '',
                'meta_og_title' => $this->settings['meta_og_title'] ?? '',
                'meta_og_description' => $this->settings['meta_og_description'] ?? '',
                'meta_og_type' => $this->settings['meta_og_type'] ?? '',
                'meta_og_url' => $this->settings['meta_og_url'] ?? '',
                'meta_og_image' => $this->settings['meta_og_image'] ?? '',
                'meta_og_site_name' => $this->settings['meta_og_site_name'] ?? '',
            ],
            'is_active' => $this->is_active,
            'is_home' => $this->is_home,
            'is_featured' => $this->is_featured,
            'is_protected' => $this->is_protected,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
