<?php

namespace Tests\Unit\Services\Product;

use App\Enums\MorphEntity;
use App\Http\Resources\Product\ProductListResource;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\ShippingRestriction;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductShippingRestrictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProductShippingRestrictionServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected ProductShippingRestrictionService $service;
    protected ProductRepository $productRepository;
    protected ShippingMethod $shippingMethod;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->mock(ProductRepository::class);
        $this->service = new ProductShippingRestrictionService($this->productRepository);
        $this->shippingMethod = ShippingMethod::factory()->create();
        $this->product = Product::factory()->create();

        // Mock request for validateRequest method
        $request = new Request(['restriction_id' => $this->product->id]);
        app()->instance('request', $request);
    }

    public function testValidateRequestPassesWithValidRestrictionId(): void
    {
        $this->assertTrue($this->service->validateRequest());
    }

    public function testValidateRequestFailsWithInvalidRestrictionId(): void
    {
        $request = new Request(['restriction_id' => 99999]); //Non existent ID
        app()->instance('request', $request);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->service->validateRequest();
    }

    public function testStoreShippingRestriction(): void
    {
        $data = ['restriction_id' => $this->product->id, 'some_other_data' => 'test'];

        $shippingRestriction = $this->service->storeShippingRestriction($this->shippingMethod, $data);

        $this->assertInstanceOf(ShippingRestriction::class, $shippingRestriction);
        $this->assertEquals(MorphEntity::PRODUCT, $shippingRestriction->restrictionable_type);
        $this->assertEquals($this->product->id, $shippingRestriction->restrictionable_id);
        $this->assertDatabaseHas('shipping_restrictions', [
            'restrictionable_type' => MorphEntity::PRODUCT,
            'restrictionable_id' => $this->product->id,
            'shipping_method_id' => $this->shippingMethod->id,
        ]);
    }

    public function testStoreShippingRestrictionThrowsExceptionOnFailure(): void
    {
        $this->shippingMethod = $this->mock(ShippingMethod::class);
        $this->shippingMethod->expects($this->once())
                             ->method('restrictions')
                             ->willReturnSelf();
        $this->shippingMethod->expects($this->once())
                             ->method('save')
                             ->willReturn(false);


        $data = ['restriction_id' => $this->product->id];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error creating shipping restriction');

        $this->service->storeShippingRestriction($this->shippingMethod, $data);
    }

    public function testUpdateShippingRestriction(): void
    {
        $shippingRestriction = ShippingRestriction::factory()->create([
            'shipping_method_id' => $this->shippingMethod->id,
            'restrictionable_type' => MorphEntity::PRODUCT,
            'restrictionable_id' => $this->product->id,
        ]);
        $data = ['some_other_data' => 'updated_test'];

        $updatedShippingRestriction = $this->service->updateShippingRestriction($shippingRestriction, $data);

        $this->assertInstanceOf(ShippingRestriction::class, $updatedShippingRestriction);
        $this->assertEquals('updated_test', $updatedShippingRestriction->some_other_data);
        $this->assertDatabaseHas('shipping_restrictions', [
            'id' => $shippingRestriction->id,
            'some_other_data' => 'updated_test',
        ]);
    }

    public function testUpdateShippingRestrictionThrowsExceptionOnFailure(): void
    {
        $shippingRestriction = $this->mock(ShippingRestriction::class);
        $shippingRestriction->expects($this->once())
                            ->method('update')
                            ->willReturn(false);

        $data = ['some_other_data' => 'updated_test'];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error updating shipping restriction');

        $this->service->updateShippingRestriction($shippingRestriction, $data);
    }

    public function testDeleteShippingRestriction(): void
    {
        $shippingRestriction = ShippingRestriction::factory()->create([
            'shipping_method_id' => $this->shippingMethod->id,
            'restrictionable_type' => MorphEntity::PRODUCT,
            'restrictionable_id' => $this->product->id,
        ]);

        $result = $this->service->deleteShippingRestriction($shippingRestriction);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('shipping_restrictions', ['id' => $shippingRestriction->id]);
    }

    public function testDeleteShippingRestrictionThrowsExceptionOnFailure(): void
    {
        $shippingRestriction = $this->mock(ShippingRestriction::class);
        $shippingRestriction->expects($this->once())
                            ->method('delete')
                            ->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error deleting shipping restriction');

        $this->service->deleteShippingRestriction($shippingRestriction);
    }

    public function testGetRestrictionableEntityResourceData(): void
    {
        $resource = new \Illuminate\Http\Resources\Json\JsonResource(['restrictionable' => $this->product]);

        $result = $this->service->getRestrictionableEntityResourceData($resource);

        $this->assertArrayHasKey('product', $result);
        $this->assertInstanceOf(ProductListResource::class, $result['product']);
        $this->assertEquals($this->product->id, $result['product']->resource->id);
    }
}