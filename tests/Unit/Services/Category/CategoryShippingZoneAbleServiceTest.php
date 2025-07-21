<?php

namespace Tests\Unit\Services\Category;

use App\Enums\MorphEntity;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Models\ShippingZone;
use App\Repositories\CategoryRepository;
use App\Services\Category\CategoryShippingZoneAbleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CategoryShippingZoneAbleServiceTest extends TestCase
{
    use RefreshDatabase;

    private CategoryShippingZoneAbleService $categoryShippingZoneAbleService;
    private CategoryRepository $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->categoryShippingZoneAbleService = new CategoryShippingZoneAbleService($this->categoryRepository);
    }

    public function testValidateRequestSuccess(): void
    {
        $category = Category::factory()->create();
        $requestData = ['shipping_zoneable_id' => $category->id];
        $request = new Request($requestData);
        app()->instance('request', $request);

        $result = $this->categoryShippingZoneAbleService->validateRequest();

        $this->assertTrue($result);
    }

    public function testValidateRequestFailure(): void
    {
        $requestData = ['shipping_zoneable_id' => 999];
        $request = new Request($requestData);
        app()->instance('request', $request);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->categoryShippingZoneAbleService->validateRequest();
    }

    public function testSyncShippingZoneAble(): void
    {
        $shippingZone = ShippingZone::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        // Existing relationship
        $shippingZone->shippingZoneAbles()->create([
            'shipping_zoneable_id' => $category1->id,
            'shipping_zoneable_type' => MorphEntity::CATEGORY->value,
        ]);

        $data = [
            ['shipping_zoneable_id' => $category2->id], // New category to attach
        ];

        $this->categoryRepository->method('findById')
            ->willReturn($category2);

        $this->categoryShippingZoneAbleService->syncShippingZoneAble($shippingZone, $data);

        $this->assertDatabaseHas('shipping_zone_ables', [
            'shipping_zone_id' => $shippingZone->id,
            'shipping_zoneable_id' => $category2->id,
            'shipping_zoneable_type' => MorphEntity::CATEGORY->value,
        ]);

        $this->assertDatabaseMissing('shipping_zone_ables', [
            'shipping_zone_id' => $shippingZone->id,
            'shipping_zoneable_id' => $category1->id,
            'shipping_zoneable_type' => MorphEntity::CATEGORY->value,
        ]);
    }

    public function testAttachShippingZoneAble(): void
    {
        $shippingZone = ShippingZone::factory()->create();
        $category = Category::factory()->create();
        $data = ['shipping_zoneable_id' => $category->id];

        $this->categoryRepository->method('findById')
            ->willReturn($category);

        $this->categoryShippingZoneAbleService->attachShippingZoneAble($shippingZone, $data);

        $this->assertDatabaseHas('shipping_zone_ables', [
            'shipping_zone_id' => $shippingZone->id,
            'shipping_zoneable_id' => $category->id,
            'shipping_zoneable_type' => MorphEntity::CATEGORY->value,
        ]);
    }

    public function testAttachShippingZoneAbleThrowsExceptionIfCategoryNotFound(): void
    {
        $shippingZone = ShippingZone::factory()->create();
        $data = ['shipping_zoneable_id' => 1];

        $this->categoryRepository->method('findById')
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Category not found');

        $this->categoryShippingZoneAbleService->attachShippingZoneAble($shippingZone, $data);
    }

    public function testDetachShippingZoneAble(): void
    {
        $shippingZone = ShippingZone::factory()->create();
        $category = Category::factory()->create();

        $shippingZone->shippingZoneAbles()->create([
            'shipping_zoneable_id' => $category->id,
            'shipping_zoneable_type' => MorphEntity::CATEGORY->value,
        ]);

        $data = ['shipping_zoneable_id' => $category->id];

        $this->categoryShippingZoneAbleService->detachShippingZoneAble($shippingZone, $data);

        $this->assertDatabaseMissing('shipping_zone_ables', [
            'shipping_zone_id' => $shippingZone->id,
            'shipping_zoneable_id' => $category->id,
            'shipping_zoneable_type' => MorphEntity::CATEGORY->value,
        ]);
    }

    public function testGetShippingZoneableEntityResourceData(): void
    {
        $category = Category::factory()->create();
        $shippingZoneAble = $category->shippingZoneAbles()->create([
            'shipping_zone_id' => ShippingZone::factory()->create()->id,
            'shipping_zoneable_type' => MorphEntity::CATEGORY->value
        ]);

        $resource = new \Illuminate\Http\Resources\Json\JsonResource(
            (object)['shippingZoneAble' => $shippingZoneAble]
        );

        $result = $this->categoryShippingZoneAbleService->getShippingZoneableEntityResourceData($resource);

        $this->assertArrayHasKey('category', $result);
        $this->assertInstanceOf(CategoryResource::class, $result['category']);
        $this->assertEquals($category->id, $result['category']->resource->shipping_zoneable_id);
    }
}