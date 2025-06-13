<?php

namespace App\Http\Controllers\Api\Price\Type;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Services\PriceType\PriceTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkPriceTypeController extends Controller
{

    public function __construct(
        private PriceTypeService $priceTypeService,
    )
    {
    }

    public function destroy(Request $request) {
        $this->priceTypeService->setUser($request->user()->user);
        $this->priceTypeService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('price_types')->validate();
        if (
            !$this->priceTypeService->destroyBulkPriceTypes(
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing price types',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Price types removed',
        ], Response::HTTP_OK);
    }
}
