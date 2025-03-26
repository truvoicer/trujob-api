<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'filesystem' => $this->filesystem,
            'category' => $this->category,
            'alt' => $this->alt,
            'url' => $this->url,
            'path' => $this->path,
        ];
    }
}
