<?php

namespace App\Http\Controllers\Api\Product;

use App\Enums\Product\ProductUnit;
use App\Http\Controllers\Controller;

class ProductUnitController extends Controller
{


    public function index()
    {
        return response()->json([
            'message' => 'Product unit types retrieved successfully',
            'data' => ProductUnit::cases(),
        ]);
    }
}
