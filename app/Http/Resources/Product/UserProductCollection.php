<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pagination;
use App\Models\Product;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserProductCollection extends BaseCollection
{
    use Pagination;

    public $collects = UserProductListResource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'products' => $this->collection,
            'links' => $this->buildLinks($this->resource)
        ];
    }
}
