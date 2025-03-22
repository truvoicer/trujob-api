<?php

namespace App\Http\Resources;

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
            'type' => $this->type,
            'title' => $this->pivot?->title,
            'subtitle' => $this->pivot?->subtitle,
            'background_image' => $this->pivot?->background_image,
            'background_color' => $this->pivot?->background_color,
            'pagination_type' => $this->pivot?->pagination_type,
            'pagination' => (bool)$this->pivot?->pagination,
            'pagination_scroll_type' => $this->pivot?->pagination_scroll_type,
            'content' => $this->pivot?->content,
            'properties' => json_decode($this->pivot?->properties),
            'has_sidebar' => (bool)$this->pivot?->has_sidebar,
            'sidebar_widgets' => json_decode($this->pivot?->sidebar_widgets),
            'order' => $this->pivot->order,
        ];
    }
}
