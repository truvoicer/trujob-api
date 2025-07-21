<?php

namespace Tests\Unit\Services\Product;

use App\Enums\Product\ProductFetchProperty;
use App\Http\Requests\Product\ProductFetchRequest;
use App\Models\Product;
use App\Models\User;
use App\Services\Product\ProductFetchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProductFetchServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductFetchService $productFetchService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productFetchService = new ProductFetchService();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function testHandleRequestConvertsStringsToArray(): void
    {
        $data = [
            ProductFetchProperty::CATEGORIES->value => 'category1,category2',
            ProductFetchProperty::TYPE->value => 'type1,type2',
            ProductFetchProperty::USER->value => 'user1,user2',
        ];

        $request = new ProductFetchRequest();
        $request->replace($data);

        // Mock the validator to always pass for testing purposes
        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->passes());


        $result = $this->productFetchService->handleRequest($request);

        $this->assertIsArray($result[ProductFetchProperty::CATEGORIES->value]);
        $this->assertIsArray($result[ProductFetchProperty::TYPE->value]);
        $this->assertIsArray($result[ProductFetchProperty::USER->value]);

        $this->assertEquals(['category1', 'category2'], $result[ProductFetchProperty::CATEGORIES->value]);
        $this->assertEquals(['type1', 'type2'], $result[ProductFetchProperty::TYPE->value]);
        $this->assertEquals(['user1', 'user2'], $result[ProductFetchProperty::USER->value]);
    }

    public function testProductsFetchWithoutDataReturnsCollection(): void
    {
        Product::factory()->count(3)->create();

        $result = $this->productFetchService->productsFetch();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
        $this->assertEquals(3, $this->productFetchService->getTotal());
    }

    public function testProductsFetchWithDataReturnsFilteredCollection(): void
    {
        Product::factory()->create(['name' => 'Product A']);
        Product::factory()->create(['name' => 'Product B']);

        $data = ['name' => 'Product A'];

        $result = $this->productFetchService->productsFetch($data);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals('Product A', $result->first()->name);
        $this->assertEquals(1, $this->productFetchService->getTotal());
    }

    public function testProductsFetchWithPaginationReturnsLengthAwarePaginator(): void
    {
        Product::factory()->count(15)->create();

        $this->productFetchService->setPagination(true);
        $this->productFetchService->setLimit(10);

        $result = $this->productFetchService->productsFetch();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(10, $result->items());
        $this->assertEquals(15, $this->productFetchService->getTotal());
    }

    public function testUserProductsFetchWithoutDataReturnsCollection(): void
    {
        Product::factory()->count(2)->create(['user_id' => $this->user->id]);
        Product::factory()->count(1)->create(); //Another user product

        $result = $this->productFetchService->userProductsFetch();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals(2, $this->productFetchService->getTotal());
    }

    public function testUserProductsFetchWithDataReturnsFilteredCollection(): void
    {
        Product::factory()->create(['user_id' => $this->user->id, 'name' => 'User Product A']);
        Product::factory()->create(['user_id' => $this->user->id, 'name' => 'User Product B']);
        Product::factory()->create(['name' => 'Other Product']);

        $data = ['name' => 'User Product A'];

        $result = $this->productFetchService->userProductsFetch($data);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals('User Product A', $result->first()->name);
        $this->assertEquals(1, $this->productFetchService->getTotal());
    }

    public function testUserProductsFetchWithPaginationReturnsLengthAwarePaginator(): void
    {
        Product::factory()->count(15)->create(['user_id' => $this->user->id]);

        $this->productFetchService->setPagination(true);
        $this->productFetchService->setLimit(10);

        $result = $this->productFetchService->userProductsFetch();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(10, $result->items());
        $this->assertEquals(15, $this->productFetchService->getTotal());
    }

}