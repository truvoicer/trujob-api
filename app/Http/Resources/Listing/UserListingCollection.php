<?php

namespace App\Http\Resources\Listing;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pagination;
use App\Models\Listing;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserListingCollection extends BaseCollection
{
    use Pagination;

    public $collects = UserListingListResource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'listings' => $this->collection,
            'links' => $this->buildLinks($this->resource)
        ];
    }
}
