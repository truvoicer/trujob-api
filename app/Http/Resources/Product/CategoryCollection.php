<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Pagination;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    use Pagination;

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'categories' => $this->collection,
            'links' => $this->buildLinks($this->resource)
        ];
    }
}
