<?php

namespace App\Http\Controllers\Api\Product;

use App\Enums\Product\ProductWeightUnit;
use App\Http\Controllers\Controller;

class ProductWeightUnitController extends Controller
{


    public function index()
    {
        return response()->json([
            'message' => 'Product weight units retrieved successfully',
            'data' => ProductWeightUnit::cases(),
        ]);
    }
}
