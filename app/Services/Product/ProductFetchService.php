<?php

namespace App\Services\Product;

use App\Enums\Product\ProductFetchProperty;
use App\Helpers\Tools\UtilHelpers;
use App\Http\Requests\Product\ProductFetchRequest;
use App\Models\Product;
use App\Services\BaseService;
use App\Services\FetchService;
use App\Traits\Product\ProductTrait;

class ProductFetchService extends BaseService
{
    use FetchService, ProductTrait;

    public function handleRequest(ProductFetchRequest $request): array
    {
        $requestData = $request->validated();
        if (!empty($requestData[ProductFetchProperty::CATEGORIES->value])) {
            $requestData[ProductFetchProperty::CATEGORIES->value] = UtilHelpers::stringToArray(
                $requestData[ProductFetchProperty::CATEGORIES->value]
            );
        }
        if (!empty($requestData[ProductFetchProperty::TYPE->value])) {
            $requestData[ProductFetchProperty::TYPE->value] = UtilHelpers::stringToArray(
                $requestData[ProductFetchProperty::TYPE->value]
            );
        }
        if (!empty($requestData[ProductFetchProperty::USER->value])) {
            $requestData[ProductFetchProperty::USER->value] = UtilHelpers::stringToArray(
                $requestData[ProductFetchProperty::USER->value]
            );
        }
        return $requestData;
    }

    public function productsFetch(?array $data = [])
    {
        $product = $this->buildProductsQuery(Product::query(), $data);

        if ($this->getPagination()) {
            $results = $product->paginate(
                $this->getLimit(),
                ['*'],
                'page',
                $this->getPage() ?? null
            );
            $this->setTotal($results->total());
            return $results;
        }
        $results = $product->get();
        $this->setTotal($results->count());
        return $results;
    }

    public function userProductsFetch(?array $data = [])
    {
        $product = $this->getUser()->product();

        $product = $this->buildProductsQuery($this->getUser()->product(), $data);
        if ($this->getPagination()) {
            $results = $product->paginate(
                $this->getLimit(),
                ['*'],
                'page',
                $this->getPage() ?? null
            );
            $this->setTotal($results->total());
            return $results;
        }
        $results = $product->get();
        $this->setTotal($results->count());
        return $results;
    }

}
