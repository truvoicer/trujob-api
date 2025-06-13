<?php

namespace App\Http\Controllers\Api\Product\Type;

use App\Helpers\Tools\ValidationHelpers;
use App\Http\Controllers\Api\Product\ProductBaseController;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BulkProductController extends ProductBaseController
{

    public function destroy(Product $product, Request $request)
    {
        $this->productsAdminService->setUser($request->user()->user);
        $this->productsAdminService->setSite($request->user()->site);

        ValidationHelpers::validateBulkIdExists('product_types')->validate();
        if (
            !$this->productsAdminService->bulkDeleteProducts(
                $request->get('ids', []),
            )
        ) {
            return response()->json([
                'message' => 'Error removing products',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Products removed',
        ], Response::HTTP_OK);
    }
}
