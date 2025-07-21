<?php

namespace Tests\Unit\Services\Price;

use App\Models\Price;
use App\Services\Price\PriceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PriceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PriceService $priceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->priceService = new PriceService();
    }

    public function testCreatePrice(): void
    {
        $data = [
            'price' => 100.00,
            'discount' => 10.00,
            'tax' => 5.00,
        ];

        $user = \App\Models\User::factory()->create();
        Auth::setUser($user);


        $this->assertTrue($this->priceService->createPrice($data));

        $this->assertDatabaseHas('prices', [
            'price' => 100.00,
            'discount' => 10.00,
            'tax' => 5.00,
            'created_by_user_id' => $user->id
        ]);
    }

    public function testUpdatePrice(): void
    {
        $price = Price::factory()->create();
        $data = [
            'price' => 120.00,
            'discount' => 15.00,
            'tax' => 7.00,
        ];
        $user = \App\Models\User::factory()->create();
        Auth::setUser($user);

        $this->assertTrue($this->priceService->updatePrice($price, $data));

        $this->assertDatabaseHas('prices', [
            'id' => $price->id,
            'price' => 120.00,
            'discount' => 15.00,
            'tax' => 7.00,
            'created_by_user_id' => $user->id
        ]);
    }

    public function testDeletePrice(): void
    {
        $price = Price::factory()->create();

        $this->assertTrue($this->priceService->deletePrice($price));

        $this->assertDatabaseMissing('prices', [
            'id' => $price->id,
        ]);
    }

    public function testDestroyBulkPrices(): void
    {
        $prices = Price::factory()->count(3)->create();
        $ids = $prices->pluck('id')->toArray();

        $this->assertTrue($this->priceService->destroyBulkPrices($ids));

        foreach ($ids as $id) {
            $this->assertDatabaseMissing('prices', [
                'id' => $id,
            ]);
        }
    }

    public function testDestroyBulkPricesThrowsExceptionWhenNoPricesFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No prices found for the given IDs');

        $this->priceService->destroyBulkPrices([1, 2, 3]);
    }

    public function testDestroyBulkPricesThrowsExceptionWhenDeleteFails(): void
    {
        $this->expectException(\Exception::class);

        $price = Price::factory()->create();
        $ids = [$price->id];

        // Mock the deletePrice method to throw an exception.
        $priceServiceMock = $this->getMockBuilder(PriceService::class)
            ->onlyMethods(['deletePrice'])
            ->getMock();

        $priceServiceMock->method('deletePrice')
            ->willReturn(false);

        try {
            $priceServiceMock->destroyBulkPrices($ids);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            $this->assertSame('Error deleting price with ID: ' . $price->id, $e->getMessage());
            throw $e; // Re-throw the exception to satisfy PHPUnit.
        }
    }
}
