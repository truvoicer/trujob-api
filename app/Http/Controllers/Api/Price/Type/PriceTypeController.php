<?php
namespace App\Http\Controllers\Api\Price\Type;

use App\Enums\Price\PriceType;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class PriceTypeController extends Controller
{

    public function index()
    {
        return response()->json([
            'data' => PriceType::buildList(),
        ], Response::HTTP_OK);
    }

}
