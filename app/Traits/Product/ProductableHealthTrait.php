<?php

namespace App\Traits\Product;

trait ProductableHealthTrait
{
    public const REQUIRED_PROPERTIES = [
        'sku',
        'name',
        'title',
        'price',
    ];
    public function healthCheck(): array
    {
        $healthCheckData = [];
        $healthCheckData['sku'] = array_merge(
            $this->skuCheck(),
            ['required' => in_array('sku', self::REQUIRED_PROPERTIES)]
        );
        $healthCheckData['name'] = array_merge(
            $this->nameCheck(),
            ['required' => in_array('name', self::REQUIRED_PROPERTIES)]
        );
        $healthCheckData['title'] = array_merge(
            $this->titleCheck(),
            ['required' => in_array('title', self::REQUIRED_PROPERTIES)]
        );
        $healthCheckData['price'] = array_merge(
            $this->priceCheck(),
            ['required' => in_array('price', self::REQUIRED_PROPERTIES)]
        );

        $healthCheckData['active'] = array_merge(
            $this->activeCheck(),
            ['required' => in_array('active', self::REQUIRED_PROPERTIES)]
        );

        $healthyItems = array_filter($healthCheckData, function ($item) {
            return $item['is_healthy'] === true;
        });
        $unhealthyItems = array_filter($healthCheckData, function ($item) {
            return $item['is_healthy'] === false;
        });
        return [
            'unhealthy' => [
                'count' => count($unhealthyItems),
                'items' => $unhealthyItems,
            ],
            'healthy' => [
                'count' => count($healthyItems),
                'items' => $healthyItems,
            ]
        ];
    }

    private function addItemHealthCheckData(
        bool $isHealthy,
        string $message,
    ): array {
        return [
            'is_healthy' => $isHealthy,
            'message' => $message,
        ];
    }

    private function activeCheck(): array
    {
        if (!$this->active) {
            return $this->addItemHealthCheckData(
                false,
                'Product is not active.'
            );
        }
        return $this->addItemHealthCheckData(
            true,
            'Product is active.'
        );
    }

    private function skuCheck(): array
    {
        if (empty($this->sku)) {
            return $this->addItemHealthCheckData(
                false,
                'Product SKU is missing.'
            );
        }
        return $this->addItemHealthCheckData(
            true,
            'Product SKU is valid.'
        );
    }

    private function nameCheck(): array
    {
        if (empty($this->name)) {
            return $this->addItemHealthCheckData(
                false,
                'Product name is missing.'
            );
        }
        return $this->addItemHealthCheckData(
            true,
            'Product name is valid.'
        );
    }

    private function titleCheck(): array
    {
        if (empty($this->title)) {
            return $this->addItemHealthCheckData(
                false,
                'Product title is missing.'
            );
        }
        return $this->addItemHealthCheckData(
            true,
            'Product title is valid.'
        );
    }

    private function priceCheck(): array
    {
        if ($this->prices->isEmpty()) {
            return $this->addItemHealthCheckData(
                false,
                'Product has no prices.'
            );
        }
        return $this->addItemHealthCheckData(
            true,
            'Product has valid prices.'
        );
    }
}
