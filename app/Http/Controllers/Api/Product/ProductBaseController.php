<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductsAdminService;
use App\Services\Product\ProductFetchService;

class ProductBaseController extends Controller
{
    protected ProductsAdminService $productsAdminService;
    protected ProductFetchService $productsFetchService;
    protected ProductRepository $productRepository;

    public function __construct()
    {
        $this->productsAdminService = app(ProductsAdminService::class);
        $this->productsFetchService = app(ProductFetchService::class);
        $this->productRepository = app(ProductRepository::class);
    }
}
