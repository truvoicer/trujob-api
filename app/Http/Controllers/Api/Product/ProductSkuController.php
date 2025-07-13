<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Requests\Product\UpdateProductSkuRequest;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;

class ProductSkuController extends ProductBaseController
{

    public function update(Product $product, UpdateProductSkuRequest $request)
    {
        $this->productsAdminService->setUser($request->user()->user);
        $this->productsAdminService->setSite($request->user()->site);

        switch ($request->validated('type')) {
            case 'generate':
                $sku = $product->generateSku();
                break;
            case 'custom':
                $sku = $request->validated('sku');
                break;
            default:
                return response()->json([
                    'message' => 'Invalid SKU type',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        if (!$this->productsAdminService->updateProduct(
            $product,
            ['sku' => $sku]
        )) {
            return response()->json([
                'message' => 'Error updating product',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $product->refresh();
        return response()->json([
            'message' => 'Product updated',
            'data' => [
                'sku' => $product->sku,
            ],
        ], Response::HTTP_OK);
    }
}
