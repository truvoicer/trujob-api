<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductAdminService;
use App\Services\Product\ProductFetchService;

class ProductBaseController extends Controller
{
    protected ProductAdminService $productsAdminService;
    protected ProductFetchService $productsFetchService;
    protected ProductRepository $productRepository;

    public function __construct()
    {
        $this->productsAdminService = app(ProductAdminService::class);
        $this->productsFetchService = app(ProductFetchService::class);
        $this->productRepository = app(ProductRepository::class);
    }
}
