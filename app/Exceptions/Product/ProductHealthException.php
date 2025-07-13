<?php

namespace App\Exceptions\Product;

use App\Models\Product;
use Exception;

class ProductHealthException extends Exception
{
    protected array $healthCheckData;
    protected Product $product;

    public function __construct(
        Product $product,
        array $healthCheckData,
        ?string $message = 'Product health check failed.'
    ) {
        parent::__construct($message);
        $this->healthCheckData = $healthCheckData;
        $this->product = $product;
    }

    public function getHealthCheckData(): array
    {
        return $this->healthCheckData;
    }

    public function render($request)
    {
        return response()->json([
            'error' => $this->getMessage(),
            'data' => [
                'product_id' => $this->product->id,
                'product_name' => $this->product->name,
                'health_check_data' => $this->getHealthCheckData(),
            ],
        ], 422);
    }
}
