<?php

namespace App\Http\Controllers\Api\Price;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Services\Price\PriceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkPriceController extends Controller
{

    public function __construct(
        private PriceService $priceService,
    ) {
    }

    public function destroy(Price $price, Request $request) {
        $this->priceService->setUser($request->user()->user);
        $this->priceService->setSite($request->user()->site);
        ValidationHelpers::validateBulkIdExists('prices')->validate();
        if (
            !$this->priceService->destroyBulkPrices(
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing prices',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Prices removed',
        ], Response::HTTP_OK);
    }
}
