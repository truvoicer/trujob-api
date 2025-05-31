<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductsAdminService;
use App\Services\Product\ProductsFetchService;

class ProductBaseController extends Controller
{
    protected ProductsAdminService $productsAdminService;
    protected ProductsFetchService $productsFetchService;
    protected ProductRepository $productRepository;

    public function __construct()
    {
        $this->productsAdminService = app(ProductsAdminService::class);
        $this->productsFetchService = app(ProductsFetchService::class);
        $this->productRepository = app(ProductRepository::class);
    }
}
