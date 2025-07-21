<?php

namespace Tests\Unit\Services\Product\Shipping\Method;

use App\Models\Product;
use App\Models\ShippingMethod;
use App\Repositories\ShippingMethodRepository;
use App\Services\Product\Shipping\Method\ProductShippingMethodService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class ProductShippingMethodServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductShippingMethodService $productShippingMethodService;

    protected MockInterface $shippingMethodRepository;

    protected Product $product;

    protected ShippingMethod $shippingMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shippingMethodRepository = Mockery::mock(ShippingMethodRepository::class);
        $this->productShippingMethodService = new ProductShippingMethodService($this->shippingMethodRepository);
        $this->product = Product::factory()->create();
        $this->shippingMethod = ShippingMethod::factory()->create();
    }

    public function test_attach_bulk_shipping_methods_to_productable_attaches_methods_successfully(): void
    {
        $shippingMethodIds = [1, 2, 3];
        $this->productShippingMethodService->attachBulkShippingMethodsToProductable($this->product, $shippingMethodIds);
        $this->assertCount(0, array_filter($shippingMethodIds, function ($item) {
                return !$this->product->productableShippingMethods()
                    ->where('shipping_method_id', $item)
                    ->exists();
            }));
        $this->assertDatabaseCount('productable_shipping_methods', count($shippingMethodIds));
    }

    public function test_attach_bulk_shipping_methods_to_productable_throws_exception_if_not_product_instance(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Productable must be an instance of Product');

        $model = new class extends Model {}; // Dummy Model
        $this->productShippingMethodService->attachBulkShippingMethodsToProductable($model, [1, 2, 3]);
    }

    public function test_detach_bulk_shipping_methods_from_productable_detaches_methods_successfully(): void
    {
        $shippingMethodIds = [1, 2, 3];
        foreach ($shippingMethodIds as $shippingMethodId) {
            $this->product->productableShippingMethods()->create([
                'shipping_method_id' => $shippingMethodId,
            ]);
        }

        $this->productShippingMethodService->detachBulkShippingMethodsFromProductable($this->product, $shippingMethodIds);

        $this->assertDatabaseCount('productable_shipping_methods', 0);
    }

    public function test_detach_bulk_shipping_methods_from_productable_throws_exception_if_not_product_instance(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Productable must be an instance of Product');

        $model = new class extends Model {}; // Dummy Model
        $this->productShippingMethodService->detachBulkShippingMethodsFromProductable($model, [1, 2, 3]);
    }

    public function test_sync_bulk_shipping_method_with_productable_syncs_methods_successfully(): void
    {
        $existingShippingMethodIds = [1, 2];
        $newShippingMethodIds = [2, 3, 4];

        foreach ($existingShippingMethodIds as $shippingMethodId) {
            $this->product->productableShippingMethods()->create([
                'shipping_method_id' => $shippingMethodId,
            ]);
        }

        $this->productShippingMethodService->syncBulkShippingMethodWithProductable($this->product, $newShippingMethodIds);

        $this->assertDatabaseCount('productable_shipping_methods', count($newShippingMethodIds));
        $this->assertTrue($this->product->productableShippingMethods()->where('shipping_method_id', 1)->doesntExist());
        $this->assertTrue($this->product->productableShippingMethods()->where('shipping_method_id', 2)->exists());
        $this->assertTrue($this->product->productableShippingMethods()->where('shipping_method_id', 3)->exists());
        $this->assertTrue($this->product->productableShippingMethods()->where('shipping_method_id', 4)->exists());
    }

    public function test_sync_bulk_shipping_method_with_productable_throws_exception_if_not_product_instance(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Productable must be an instance of Product');

        $model = new class extends Model {}; // Dummy Model
        $this->productShippingMethodService->syncBulkShippingMethodWithProductable($model, [1, 2, 3]);
    }

    public function test_attach_shipping_method_to_productable_attaches_method_successfully(): void
    {
        $this->productShippingMethodService->attachShippingMethodToProductable($this->product, $this->shippingMethod);

        $this->assertDatabaseHas('productable_shipping_methods', [
            'productable_id' => $this->product->id,
            'productable_type' => Product::class,
            'shipping_method_id' => $this->shippingMethod->id,
        ]);
    }

    public function test_attach_shipping_method_to_productable_does_not_attach_if_already_attached(): void
    {
        $this->product->productableShippingMethods()->create([
            'shipping_method_id' => $this->shippingMethod->id,
        ]);
        $this->productShippingMethodService->attachShippingMethodToProductable($this->product, $this->shippingMethod);

        $this->assertDatabaseCount('productable_shipping_methods', 1);
    }

    public function test_attach_shipping_method_to_productable_throws_exception_if_not_product_instance(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Productable must be an instance of Product');

        $model = new class extends Model {}; // Dummy Model
        $shippingMethod = ShippingMethod::factory()->create();

        $this->productShippingMethodService->attachShippingMethodToProductable($model, $shippingMethod);
    }

    public function test_detach_shipping_method_from_productable_detaches_method_successfully(): void
    {
        $this->product->productableShippingMethods()->create([
            'shipping_method_id' => $this->shippingMethod->id,
        ]);

        $this->productShippingMethodService->detachShippingMethodFromProductable($this->product, $this->shippingMethod);

        $this->assertDatabaseMissing('productable_shipping_methods', [
            'productable_id' => $this->product->id,
            'productable_type' => Product::class,
            'shipping_method_id' => $this->shippingMethod->id,
        ]);
    }

    public function test_detach_shipping_method_from_productable_throws_exception_if_not_product_instance(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Productable must be an instance of Product');

        $model = new class extends Model {}; // Dummy Model
        $shippingMethod = ShippingMethod::factory()->create();

        $this->productShippingMethodService->detachShippingMethodFromProductable($model, $shippingMethod);
    }
}