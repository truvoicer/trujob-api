<?php
namespace App\Http\Controllers\Api\Product\Price;

use App\Enums\Price\PriceType;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductPriceTypeController extends Controller
{

    public function index(Product $product, Request $request)
    {

        return response()->json([
            'data' => array_filter(
                PriceType::buildList(),
                function (array $priceType) use ($product) {
                    return $product->prices()
                        ->where('price_type', $priceType['name'])
                        ->exists();
                }
            ),
        ]);
    }

}
