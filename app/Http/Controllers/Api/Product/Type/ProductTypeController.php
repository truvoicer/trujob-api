<?php

namespace App\Http\Controllers\Api\Product\Type;

use App\Enums\Product\ProductType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductTypeController extends Controller
{


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json([
            'data' => ProductType::cases(),
            'message' => 'Product types retrieved successfully',
        ], Response::HTTP_OK);
    }

}
