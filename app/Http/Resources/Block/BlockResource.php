<?php

namespace App\Http\Resources\Block;

use App\Enums\PageBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
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
            'properties' => $this->properties,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }
}
